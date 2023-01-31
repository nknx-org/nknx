<?php

namespace App\Http\Controllers;

use Exception;
use Aws\Ec2\Ec2Client;
use Vultr\VultrClient;

use App\Models\FdDeployment;
use Vultr\Adapter\CurlAdapter;
use App\Models\FdConfiguration;

use Aws\Credentials\Credentials;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use LKDev\HetznerCloud\HetznerAPIClient;

class FdDeploymentController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $user = Auth::user();
        $ssh_keys = $user->ssh_keys;

        Request::validate([
            'provider' => ['required'],
            'names' => ['required'],
            'size' => ['required'],
            'region' => ['required'],
            'vps_key_id' => ['required'],
        ]);

        $provider = Request::input('provider');
        $names = explode(',', Request::input('names'));
        $size = Request::input('size');
        $region = Request::input('region');
        $vps_key_id = Request::input('vps_key_id');


        $deploymentObjects = [];

        foreach ($names as $name){

            $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
            $pass = array(); //remember to declare $pass as an array
            $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
            for ($i = 0; $i < 12; $i++) {
                $n = rand(0, $alphaLength);
                $pass[] = $alphabet[$n];
            }
            $password = implode($pass);
            $secret = sha1(md5($password));

            $fd_configuration = FdConfiguration::find($id);

            $userData = '#cloud-config
package_upgrade: true
packages:
    - unzip
    - jq
    - whois
';
            if (!$fd_configuration->disable_ufw) {
                $userData .= '    - ufw
';
            }
            $userData .= 'runcmd:
';
            if (!$fd_configuration->disable_ufw) {
                $userData .= '    - \'ufw allow 30001 > /dev/null 2>&1\'
    - \'ufw allow 30002 > /dev/null 2>&1\'
    - \'ufw allow 30003 > /dev/null 2>&1\'
    - \'ufw allow 30004 > /dev/null 2>&1\'
    - \'ufw allow 30005 > /dev/null 2>&1\'
    - \'ufw allow 30010/tcp > /dev/null 2>&1\'
    - \'ufw allow 30011/udp > /dev/null 2>&1\'
    - \'ufw allow 30020/tcp > /dev/null 2>&1\'
    - \'ufw allow 30021/udp > /dev/null 2>&1\'
    - \'ufw allow 32768:65535/tcp > /dev/null 2>&1\'
    - \'ufw allow 32768:65535/udp > /dev/null 2>&1\'
    - \'ufw allow 22 > /dev/null 2>&1\'
    - \'ufw allow 80 > /dev/null 2>&1\'
    - \'ufw allow 443 > /dev/null 2>&1\'
    - \'ufw --force enable > /dev/null 2>&1\'
';
            }
            if($ssh_keys->count()){
            $userData .= '    - \'sed -i "/PasswordAuthentication yes/d" /etc/ssh/sshd_config\'
    - \'echo "" | sudo tee -a /etc/ssh/sshd_config\'
    - \'echo "" | sudo tee -a /etc/ssh/sshd_config\'
    - \'echo "PasswordAuthentication no" | sudo tee -a /etc/ssh/sshd_config\'
    - \'ssh-keygen -A\'
    - \'service ssh restart\'
    - \'if [ ! -d /root/.ssh ]\'
    - \'then\'
    - \'    mkdir -p /root/.ssh\'
    - \'    touch /root/.ssh/authorized_keys\'
    - \'fi\'
';
            }
            $userData .= '    - \'curl --insecure --data "secret=' . $secret . '" ' . config('app.created_callback') . '\'
    - \'useradd nknx\'
    - \'mkdir -p /home/nknx/.ssh\'
';
    if($ssh_keys->count()){
$userData .= '    - \'cat > /root/.ssh/authorized_keys << EOF\'
    - \'# SSH keys allowed through NKNx FastDeploy\'
';
    foreach($ssh_keys as $ssh_key){
        $userData .= '    - \'' . $ssh_key->pubkey . '\'
';
    }
    $userData .= '    - \'\'
    - \'\'
    - \'EOF\'
    - \'cp /root/.ssh/authorized_keys /home/nknx/.ssh/authorized_keys\'
';
}

$userData .= '    - \'echo "'.$name.'" > /etc/hostname\'
    - \'sed -i "s/127\.0\.0\.1.*localhost/127.0.0.1 '.$name.'.localdomain '.$name.' localhost/" /etc/hosts\'
    - \'hostname '.$name.'\'
    - \'mkdir -p /home/nknx/.nknx\'
    - \'adduser nknx sudo\'
    - \'chsh -s /bin/bash nknx\'
    - \'PASSWORD=$(mkpasswd -m sha-512 ' . $password . ')\'
    - \'usermod --password $PASSWORD nknx\'
    - \'cd "/home/nknx"\'
    - \'wget https://commercial.nkn.org/downloads/nkn-commercial/linux-amd64.zip\'
    - \'unzip linux-amd64.zip\'
    - \'cd linux-amd64\'
';
if($fd_configuration->fast_sync || $fd_configuration->light_sync){
    $userData .= '    - \'cat >config.json <<EOF\'
    - \'{\'
    - \'    "nkn-node": {\'
';
if($fd_configuration->fast_sync){
    $userData .= '    - \'      "args": "--sync fast"\'
';
} else if($fd_configuration->light_sync){
    $userData .= '    - \'      "args": "--sync light"\'
';
};
$userData .= '    - \'    }\'
    - \'}\'
    - \'EOF\'
';
$userData .= '    - \'./nkn-commercial -b ' . $fd_configuration->beneficiary_addr . '  -c /home/nknx/linux-amd64/config.json -d /home/nknx/nkn-commercial -u nknx install\'
';

}
else{
    $userData .= '    - \'./nkn-commercial -b ' . $fd_configuration->beneficiary_addr . ' -d /home/nknx/nkn-commercial -u nknx install\'
';
}

$userData .= '    - \'chown -R nknx:nknx /home/nknx\'
    - \'chmod -R 755 /home/nknx\'
    - \'while [ ! -f /home/nknx/nkn-commercial/services/nkn-node/wallet.json ]; do sleep 10; done\'
    - \'addr=$(jq -r .Address /home/nknx/nkn-commercial/services/nkn-node/wallet.json)\'
';
            if ($fd_configuration->download_chain){
                $userData .= '    - \'curl --insecure --data "secret=' . $secret . '" ' . config('app.snapshot_callback')  . '\'
    - \'cd /home/nknx/nkn-commercial/services/nkn-node/\'
    - \'systemctl stop nkn-commercial.service\'
    - \'rm -rf ChainDB\'
    - \'wget -c https://nkn.org/ChainDB_pruned_latest.tar.gz -O - | tar -xz\'
    - \'chown -R nknx:nknx ChainDB/\'
    - \'systemctl start nkn-commercial.service\'
';
    }

    $userData .= '    - \'chown -R nknx:nknx /home/nknx\'
    - \'chmod -R 755 /home/nknx\'
    - \'curl --insecure --data "secret=' . $secret . '&password=' . $password . '" ' . config('app.finish_callback')  . '\'
';
            array_push($deploymentObjects,[
                "name" => $name,
                "secret" => $secret,
                "userdata" => $userData
            ]);
        }

        switch ($provider) {
            case "DigitalOcean":
                $user = Auth::user();
                $api_key = $user->vps_keys()->where('id', $vps_key_id)->where('provider', "DigitalOcean")->first();

                if ($api_key) {
                    try{

                        $client = new \DigitalOceanV2\Client();
                        $client->authenticate($api_key->api_token);

                        $droplet = $client->droplet();

                        $errorcount = 0;

                        $deployments = [];

                        foreach ($deploymentObjects as $deploymentObject){
                            try{
                                $created = $droplet->create($deploymentObject["name"], $region, $size, "ubuntu-20-04-x64", false, false, false, array(), $deploymentObject["userdata"]);
                                $deployment_obj = new FdDeployment([
                                    "provider" => "DigitalOcean",
                                    "label" => $deploymentObject["name"],
                                    "secret" => $deploymentObject["secret"],
                                    "architecture" => "linux-amd64"
                                ]);
                                $fd_configuration->fd_deployments()->save($deployment_obj);
                                array_push($deployments, $deployment_obj);
                            }
                            catch(Exception $e){
                                $errorcount++;
                            }
                            catch(\DigitalOceanV2\Exception\ValidationFailedException $e){
                                $errorcount++;
                            }
                        }
                        if (count($deployments) > 0 && !$errorcount){
                            return Redirect::back()->with('success', 'Nodes created successfully.');
                        }
                        else if (count($deployments) > 0 && $errorcount){
                            return Redirect::back()->with('warning', 'Some Nodes created successfully, but some didn\'t because your provider reported an error.');
                        }
                        else{
                            return Redirect::back()->with('error', 'Something went wrong on your Provider. Maybe you reached a server limit?');
                        }


                    } catch (Exception $ex) { // Anything that went wrong
                        return Redirect::back()->with('error', 'An error occured: ' . $ex);
                    }
                }
                break;
            case "Vultr":
                $user = Auth::user();

                $api_key = $user->vps_keys()->where('id', $vps_key_id)->where('provider', "Vultr")->first();
                if ($api_key) {
                    $client = new VultrClient(
                        new CurlAdapter($api_key->api_token)
                    );

                        $deployments = [];
                        $errorcount = 0;
                        foreach ($deploymentObjects as $deploymentObject){


                            $scriptId = $client->startupscript()->create('NKNx'. $fd_configuration->uuid,'#!/bin/sh

sudo wget -O install.sh "'. config('app.install_callback') . $fd_configuration->uuid . '/linux-amd64/' .$deploymentObject["name"] . '/true"; bash install.sh', 'boot');
                            try{
                                $created = $client->server()->create([
                                    "DCID" => $region,
                                    "VPSPLANID" => $size,
                                    "OSID" => 387,
                                    "label" => $deploymentObject["name"],
                                    "SCRIPTID" => $scriptId
                                ]);
                                $deployment_obj = new FdDeployment([
                                    "provider" => "Vultr",
                                    "label" => $deploymentObject["name"],
                                    "secret" => $deploymentObject["secret"],
                                    "architecture" => "linux-amd64"
                                ]);
                                array_push($deployments, $deployment_obj);
                            }
                            catch(\Vultr\Exception\ApiException $e){
                                $errorcount++;
                            }
                        }
                        if (count($deployments) > 0 && !$errorcount){
                            return Redirect::back()->with('success', 'Nodes created successfully.');
                        }
                        else if (count($deployments) > 0 && $errorcount){
                            return Redirect::back()->with('warning', 'Some Nodes created successfully, but some didn\'t because your provider reported an error.');
                        }
                        else{
                            return Redirect::back()->with('error', 'Something went wrong on your Provider. Maybe you reached a server limit?');
                        }
                        $user->fd_count = $user->fd_count + count($deployments);
                        $user->save();
                }
                break;
            case "Hetzner":
                    $user = Auth::user();
                    $deployments = [];
                    $api_key = $user->vps_keys()->where('id', $vps_key_id)->where('provider', "Hetzner")->first();
                    if ($api_key) {
                        $hetznerClient = new HetznerAPIClient($api_key->api_token);
                        $errorcount = 0;
                            foreach ($deploymentObjects as $deploymentObject){
                                try{
                                    $location = $hetznerClient->locations()->get($region);
                                    $image = $hetznerClient->images()->get('ubuntu-22.04');
                                    $serverType = $hetznerClient->serverTypes()->get($size);
                                    $apiResponse = $hetznerClient->servers()->createInLocation($deploymentObject["name"], $serverType, $image, $location,[],true,$deploymentObject["userdata"]);
                                    $deployment_obj = new FdDeployment([
                                        "provider" => "Hetzner",
                                        "label" => $deploymentObject["name"],
                                        "secret" => $deploymentObject["secret"],
                                        "architecture" => "linux-amd64"
                                    ]);
                                    $fd_configuration->fd_deployments()->save($deployment_obj);
                                    array_push($deployments, $deployment_obj);
                                }
                                catch(\ErrorException $e){
                                    $errorcount++;
                                }
                            }
                            if (count($deployments) > 0 && !$errorcount){
                                return Redirect::back()->with('success', 'Nodes created successfully.');
                            }
                            else if (count($deployments) > 0 && $errorcount){
                                return Redirect::back()->with('warning', 'Some Nodes created successfully, but some didn\'t because your provider reported an error.');
                            }
                            else{
                                return Redirect::back()->with('error', 'Something went wrong on your Provider. Maybe you reached a server limit?');
                            }
                            $user->fd_count = $user->fd_count + count($deployments);
                            $user->save();
                    }
                break;
            case "AWS":
                $user = Auth::user();
                $api_key = $user->vps_keys()->where('id', $vps_key_id)->where('provider', "AWS")->first();

                if ($api_key->api_token && $api_key->api_secret) {
                    $credentials = new Credentials($api_key->api_token, $api_key->api_secret);

                    $ec2Client = new Ec2Client(array(
                        'credentials' => $credentials,
                        'region' => $region,
                        'version' => 'latest'
                    ));

                    $imagesSearch = $ec2Client->DescribeImages([
                        'Filters' => [
                            [
                                'Name' => 'name',
                                'Values' => ['ubuntu/images/hvm-ssd/*22.04-amd64-server-????????']
                            ],
                            [
                                'Name' => 'state',
                                'Values' => ['available']
                            ],
                        ]
                    ]);

                    $ami = $imagesSearch->search('sort_by(Images, &CreationDate)[-1:].[ImageId, BlockDeviceMappings]')[0][0];

                    //increase first hdd to 50GB size
                    $blockDeviceMappings = $imagesSearch->search('sort_by(Images, &CreationDate)[-1:].[ImageId, BlockDeviceMappings]')[0][1];
                    $blockDeviceMappings[0]["Ebs"]["VolumeSize"] = 50;

                    $securityGroupName = "";

                    $result = $ec2Client->describeSecurityGroups([
                        'Filters' => [
                            [
                                'Name' => 'group-name',
                                'Values' => ['NKNx security group']
                            ]
                        ]
                    ]);

                    if(!$result['SecurityGroups']){
                        // Create the security group
                        $securityGroupName = 'NKNx security group';
                        $result = $ec2Client->createSecurityGroup(array(
                            'GroupName'   => $securityGroupName,
                            'Description' => 'Security group for running NKN nodes'
                        ));
                        $securityGroupId = $result->get('GroupId');

                        // Set ingress rules for the security group
                        $ec2Client->authorizeSecurityGroupIngress(array(
                            'GroupName'     => $securityGroupName,
                            'IpPermissions' => array(
                                array(
                                    'IpProtocol' => 'tcp',
                                    'FromPort'   => 22,
                                    'ToPort'     => 22,
                                    'IpRanges'   => array(
                                        array('CidrIp' => '0.0.0.0/0')
                                    ),
                                ),
                                array(
                                    'IpProtocol' => 'tcp',
                                    'FromPort'   => 30001,
                                    'ToPort'     => 30005,
                                    'IpRanges'   => array(
                                        array('CidrIp' => '0.0.0.0/0')
                                    ),
                                ),
                                array(
                                    'IpProtocol' => 'tcp',
                                    'FromPort'   => 30010,
                                    'ToPort'     => 30010,
                                    'IpRanges'   => array(
                                        array('CidrIp' => '0.0.0.0/0')
                                    ),
                                ),
                                array(
                                    'IpProtocol' => 'udp',
                                    'FromPort'   => 30011,
                                    'ToPort'     => 30011,
                                    'IpRanges'   => array(
                                        array('CidrIp' => '0.0.0.0/0')
                                    ),
                                ),
                                array(
                                    'IpProtocol' => 'tcp',
                                    'FromPort'   => 30020,
                                    'ToPort'     => 30020,
                                    'IpRanges'   => array(
                                        array('CidrIp' => '0.0.0.0/0')
                                    ),
                                ),
                                array(
                                    'IpProtocol' => 'udp',
                                    'FromPort'   => 30021,
                                    'ToPort'     => 30021,
                                    'IpRanges'   => array(
                                        array('CidrIp' => '0.0.0.0/0')
                                    ),
                                ),
                                array(
                                    'IpProtocol' => 'tcp',
                                    'FromPort'   => 32768,
                                    'ToPort'     => 65535,
                                    'IpRanges'   => array(
                                        array('CidrIp' => '0.0.0.0/0')
                                    ),
                                ),
                                array(
                                    'IpProtocol' => 'udp',
                                    'FromPort'   => 32768,
                                    'ToPort'     => 65535,
                                    'IpRanges'   => array(
                                        array('CidrIp' => '0.0.0.0/0')
                                    ),
                                )
                            )
                        ));
                    }
                    else{
                        $securityGroupName = $result['SecurityGroups'][0]["GroupName"];
                    }

                    $deployments = [];
                    $errorcount = 0;
                    foreach ($deploymentObjects as $deploymentObject){
                        try{
                            $ec2Client->runInstances(array(
                                'BlockDeviceMappings' => $blockDeviceMappings,
                                'ImageId'        => $ami,
                                'MinCount'       => 1,
                                'MaxCount'       => 1,
                                'InstanceType'   => $size,
                                'SecurityGroups' => array($securityGroupName),
                                'UserData'       => base64_encode($deploymentObject["userdata"])
                            ));

                            $deployment_obj = new FdDeployment([
                                "provider" => "AWS",
                                "label" => $deploymentObject["name"],
                                "secret" => $deploymentObject["secret"],
                                "architecture" => "linux-amd64"
                            ]);
                            $fd_configuration->fd_deployments()->save($deployment_obj);
                            array_push($deployments, $deployment_obj);
                        }
                        catch(\Aws\Ec2\Exception\Ec2Exception $e){
                            $errorcount++;
                        }
                    }
                    if (count($deployments) > 0 && !$errorcount){
                        return Redirect::back()->with('success', 'Nodes created successfully.');
                    }
                    else if (count($deployments) > 0 && $errorcount){
                        return Redirect::back()->with('warning', 'Some Nodes created successfully, but some didn\'t because your provider reported an error.');
                    }
                    else{
                        return Redirect::back()->with('error', 'Something went wrong on your Provider. Maybe you reached a server limit?');
                    }
                    $user->fd_count = $user->fd_count + count($deployments);
                    $user->save();
                }
                break;
        }
    }

    /**
     * Get deployment_entry install script
     *
     * Returns a generated deployment_entry install shell script based on the uid
     *
     * @queryParam uid required generated uid
     *     *
     */
    public function customInstaller(Request $request, $uuid, $architecture , $label, $vultr = false)
    {
        $fd_configuration = FdConfiguration::where("uuid", $uuid)->first();

        if ($fd_configuration) {

            $user = $fd_configuration->user;

            $ssh_keys = $user->ssh_keys;


            $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
            $pass = array(); //remember to declare $pass as an array
            $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
            for ($i = 0; $i < 12; $i++) {
                $n = rand(0, $alphaLength);
                $pass[] = $alphabet[$n];
            }
            $password = implode($pass);
            $secret = sha1(md5($password));

            //'uuid', 'label', 'beneficiary_addr', 'download_chain','enable_web_ui', 'disable_ufw', 'user_id'
            $script = "";
            $script .= "clear\n";
            $script .= "echo \"=============================================================================================\"\n";
            $script .= "echo \"                              WELCOME TO NKNx FAST DEPLOY!\"\n";
            $script .= "echo \"=============================================================================================\"\n";
            $script .= "echo\n";
            $script .= "echo \"This script will automatically provision a node as you configured it in your snippet.\"\n";
            $script .= "echo \"So grab a coffee, lean back or do something else - installation will take about 5 minutes.\"\n";
            $script .= "echo -e \"=============================================================================================\"\n";
            $script .= "echo\n";

            $script .= "echo \"Hardening your OS...\"\n";
            $script .= "echo \"---------------------------\"\n";
            $script .= "export DEBIAN_FRONTEND=noninteractive\n";
            $script .= "apt-get -qq update\n";
            $script .= "apt-get -qq upgrade -y\n";

            $script .= "echo \"Installing necessary libraries...\"\n";
            $script .= "echo \"---------------------------\"\n";
            $script .= "apt-get install -o Dpkg::Options::=\"--force-confdef\" -o Dpkg::Options::=\"--force-confold\" -y --force-yes make curl git unzip whois\n";

            if (!$fd_configuration->disable_ufw) {
                $script .= "apt-get install -o Dpkg::Options::=\"--force-confdef\" -o Dpkg::Options::=\"--force-confold\" -y --force-yes ufw\n";
            }

            $script .= "apt-get install -o Dpkg::Options::=\"--force-confdef\" -o Dpkg::Options::=\"--force-confold\" -y --force-yes unzip jq\n";

            if (!$fd_configuration->disable_ufw) {
                $script .= "ufw allow 30001 > /dev/null 2>&1\n";
                $script .= "ufw allow 30002 > /dev/null 2>&1\n";
                $script .= "ufw allow 30003 > /dev/null 2>&1\n";
                $script .= "ufw allow 30004 > /dev/null 2>&1\n";
                $script .= "ufw allow 30005 > /dev/null 2>&1\n";
                $script .= "ufw allow 30010/tcp > /dev/null 2>&1\n";
                $script .= "ufw allow 30011/udp > /dev/null 2>&1\n";
                $script .= "ufw allow 30020/tcp > /dev/null 2>&1\n";
                $script .= "ufw allow 30021/udp > /dev/null 2>&1\n";
                $script .= "ufw allow 32768:65535/tcp > /dev/null 2>&1\n";
                $script .= "ufw allow 32768:65535/udp > /dev/null 2>&1\n";
                $script .= "ufw allow 22 > /dev/null 2>&1\n";
                $script .= "ufw --force enable > /dev/null 2>&1\n";
            }

            if($ssh_keys->count()){
                $script .= "sed -i '/PasswordAuthentication yes/d' /etc/ssh/sshd_config\n";
                $script .= "echo \"\" | sudo tee -a /etc/ssh/sshd_config\n";
                $script .= "echo \"\" | sudo tee -a /etc/ssh/sshd_config\n";
                $script .= "echo \"PasswordAuthentication no\" | sudo tee -a /etc/ssh/sshd_config\n";
                $script .= "ssh-keygen -A\n";
                $script .= "service ssh restart\n";
                $script .="if [ ! -d /root/.ssh ]\n";
                $script .="then\n";
                $script .="	mkdir -p /root/.ssh\n";
                $script .="	touch /root/.ssh/authorized_keys\n";
                $script .="fi\n";
            }

            $script .= "curl --insecure --data \"secret=" . $secret . "\" " . config('app.created_callback') . "\n";

            $script .= "useradd nknx\n";
            $script .= "mkdir -p /home/nknx/.ssh\n";
            $script .= "mkdir -p /home/nknx/.nknx\n";
            $script .= "adduser nknx sudo\n";
            $script .= "chsh -s /bin/bash nknx\n";
            $script .= "PASSWORD=$(mkpasswd -m sha-512 " . $password . ")\n";
            $script .= "usermod --password \$PASSWORD nknx > /dev/null 2>&1\n";

            if($ssh_keys->count()){
                $script .= "cat > /root/.ssh/authorized_keys << EOF\n";
                    $script .= "# SSH keys allowed through NKNx FastDeploy\n";
                    foreach($ssh_keys as $ssh_key){
                        $script .= $ssh_key->pubkey . "\n";
                    }
                    $script .= "\n";
                    $script .= "\n";
                    $script .= "EOF\n";
                    $script .= "cp /root/.ssh/authorized_keys /home/nknx/.ssh/authorized_keys\n";
            }


            $script .= "cd /home/nknx\n";

            $script .= "echo \"Installing NKN Commercial...\"\n";
            $script .= "echo \"---------------------------\"\n";
            $script .= "wget --quiet --continue --show-progress https://commercial.nkn.org/downloads/nkn-commercial/" . $architecture . ".zip > /dev/null 2>&1\n";
            $script .= "unzip -qq " . $architecture . ".zip\n";
            $script .= "cd " . $architecture . "\n";

            $script .= "cat >config.json <<EOF\n";
            $script .= "{\n";
            $script .= "    \"nkn-node\": {\n";
        if($fd_configuration->fast_sync){
            $script .= "      \"args\": \"--sync fast\",\n";
        } else if($fd_configuration->light_sync){
            $script .= "      \"args\": \"--sync light\",\n";
        };
            $script .= "      \"noRemotePortCheck\": true\n";
            $script .= "    }\n";
            $script .= "}\n";
            $script .= "EOF\n";

            $script .= "./nkn-commercial -b " . $fd_configuration->beneficiary_addr . " -c /home/nknx/" . $architecture . "/config.json -d /home/nknx/nkn-commercial -u nknx install > /dev/null 2>&1\n";
            $script .= "chown -R nknx:nknx /home/nknx\n";
            $script .= "chmod -R 755 /home/nknx\n";

            $script .= "echo \"Waiting for wallet generation...\"\n";
            $script .= "echo \"---------------------------\"\n";
            $script .= "while [ ! -f /home/nknx/nkn-commercial/services/nkn-node/wallet.json ]; do sleep 10; done\n";

            if($fd_configuration->download_chain){
                $script .= "echo \"Downloading pruned snapshot...\"\n";
                $script .= "echo \"---------------------------\"\n";
                $script .= "curl --insecure --data \"secret=" . $secret . "\" " . config('app.snapshot_callback')  . "\n";
                $script .= "cd /home/nknx/nkn-commercial/services/nkn-node/\n";
                $script .= "systemctl stop nkn-commercial.service\n";
                $script .= "rm -rf ChainDB\n";
                $script .= "wget -c https://nkn.org/ChainDB_pruned_latest.tar.gz -O - | tar -xz\n";
                $script .= "chown -R nknx:nknx ChainDB/\n";
                $script .= "systemctl start nkn-commercial.service\n";
            }
            else {
                $script .= "echo \"Chain download skipped.\"\n";
                $script .= "echo \"---------------------------\"\n";
            }


            $script .= "echo \"Applying finishing touches...\"\n";
            $script .= "echo \"---------------------------\"\n";
            $script .= "addr=$(jq -r .Address /home/nknx/nkn-commercial/services/nkn-node/wallet.json)\n";
            $script .= "pwd=$(cat /home/nknx/nkn-commercial/services/nkn-node/wallet.pswd)\n";
            $script .= "chown -R nknx:nknx /home/nknx\n";
            $script .= "chmod -R 755 /home/nknx\n";
            $script .= "curl --insecure --data \"secret=" . $secret . "&password=" . $password . "\" " . config('app.finish_callback')  . "\n";
            $script .= "sleep 2\n";
            $script .= "clear\n";
            $script .= "echo\n";
            $script .= "echo\n";
            $script .= "echo\n";
            $script .= "echo\n";
            $script .= "echo \"                                  -----------------------\"\n";
            $script .= "echo \"                                  |   NKNx FAST-DEPLOY  |\"\n";
            $script .= "echo \"                                  -----------------------\"\n";
            $script .= "echo\n";
            $script .= "echo \"=============================================================================================\"\n";
            $script .= "echo \"   NKN ADDRESS OF THIS NODE: \$addr\"\n";
            $script .= "echo \"   PASSWORD FOR THIS WALLET IS: \$pwd\"\n";
            $script .= "echo \"=============================================================================================\"\n";
            $script .= "echo \"   ALL MINED NKN WILL GO TO: " . $fd_configuration->beneficiary_addr . "\"\n";
            $script .= "echo \"=============================================================================================\"\n";
            $script .= "echo\n";
            $script .= "echo \"You can now disconnect from your terminal. The node will automatically appear in NKNx after 1 minute.\"\n";
            $script .= "echo\n";
            $script .= "echo\n";
            $script .= "echo\n";
            $script .= "echo\n";

            if($vultr){
                $deployment_obj = new FdDeployment([
                    "provider" => "Vultr",
                    "label" => $label,
                    "secret" => $secret,
                    "architecture" => $architecture
                ]);
            }
            else{
                $deployment_obj = new FdDeployment([
                    "provider" => "Custom",
                    "label" => $label,
                    "secret" => $secret,
                    "architecture" => $architecture
                ]);
            }
            $fd_configuration->fd_deployments()->save($deployment_obj);
            $user->fd_count = $user->fd_count + 1;
            $user->save();

            return $script;
        }
    }

    /**
     * Get switch to liteSync install script
     *
     * Returns a generated install shell script to switch a fastDeploy node to a liteSync node
     *
     * @queryParam uid required generated uid
     *
     *
     */
    public function switchToLiteSync(Request $request, $architecture)
    {

            $script = "";
            $script .= "clear\n";
            $script .= "echo \"=============================================================================================\"\n";
            $script .= "echo \"                       WELCOME TO THE NKNx FAST DEPLOY LITESYNC UPDATER!\"\n";
            $script .= "echo \"=============================================================================================\"\n";
            $script .= "echo\n";
            $script .= "echo \"This script will change your FastDeploy node to a LiteSync node.\"\n";
            $script .= "echo -e \"=============================================================================================\"\n";
            $script .= "echo\n";
            $script .= "read -r -p \"Do you really want to proceed? [y/n] \" response\n";
            $script .= "case \"\$response\" in\n";
            $script .= "    [yY][eE][sS]|[yY])\n";
            $script .= "        systemctl stop nkn-commercial.service\n";
            $script .= "        cd /home/nknx\n";
            $script .= "        cd " . $architecture . "\n";
            $script .= "        rm -rf /home/nknx/nkn-commercial/services/nkn-node/ChainDB\n";
            $script .= "        rm config.json\n";
            $script .= "        cat >config.json <<EOF\n";
            $script .= "        {\n";
            $script .= "            \"nkn-node\": {\n";
            $script .= "              \"args\": \"--sync light\",\n";
            $script .= "              \"noRemotePortCheck\": true\n";
            $script .= "            }\n";
            $script .= "        }\n";
            $script .= "EOF\n";
            $script .= "        systemctl start nkn-commercial.service\n";
            $script .= "echo \"---------------------------\"\n";
            $script .= "echo \"FastDeploy Node updated successfully!\"\n";
            $script .= "echo \"---------------------------\"\n";
            $script .= "esac\n";
            $script .= "sleep 2\n";
            $script .= "clear\n";
            $script .= "echo\n";
            $script .= "echo\n";
            $script .= "echo\n";
            $script .= "echo\n";
            $script .= "echo \"                                  -----------------------\"\n";
            $script .= "echo \"                                  |   UPDATE COMPLETE   |\"\n";
            $script .= "echo \"                                  -----------------------\"\n";
            $script .= "echo\n";
            $script .= "echo \"=============================================================================================\"\n";
            $script .= "echo \"              THANK YOU FOR CHOOSING NKNx FASTDEPLOY - WE HOPE TO SEE YOU SOON!\"\n";
            $script .= "echo \"=============================================================================================\"\n";
            $script .= "echo\n";
            $script .= "echo \"You can now disconnect from your terminal.\"\n";
            $script .= "echo\n";
            $script .= "echo\n";
            $script .= "echo\n";
            $script .= "echo\n";



            return $script;

    }



    /**
     * Get deployment_entry install script
     *
     * Returns a generated deployment_entry install shell script based on the uid
     *
     * @queryParam uid required generated uid
     *
     *
     */
    public function customUninstaller(Request $request)
    {

            $script = "";
            $script .= "clear\n";
            $script .= "echo \"=============================================================================================\"\n";
            $script .= "echo \"                       WELCOME TO THE NKNx FAST DEPLOY UNINSTALLER!\"\n";
            $script .= "echo \"=============================================================================================\"\n";
            $script .= "echo\n";
            $script .= "echo \"This script will automatically remove the NKN software from this server, remove the nknx user\"\n";
            $script .= "echo \"and clean up the dependencies. MAKE SURE YOU BACKED UP YOUR NKN WALLET ON THIS SERVER!\"\n";
            $script .= "echo -e \"=============================================================================================\"\n";
            $script .= "echo\n";
            $script .= "read -r -p \"Do you really want to proceed uninstalling FastDeploy (wallet will also be removed)? [y/n] \" response\n";
            $script .= "case \"\$response\" in\n";
            $script .= "    [yY][eE][sS]|[yY])\n";
            $script .= "        systemctl stop nkn-commercial.service\n";
            $script .= "        systemctl disable nkn-commercial.service\n";
            $script .= "        rm /etc/systemd/system/nkn-commercial.service\n";
            $script .= "        systemctl daemon-reload\n";
            $script .= "        userdel -r nknx > /dev/null 2>&1\n";
            $script .= "echo \"---------------------------\"\n";
            $script .= "echo \"FastDeploy removed successfully!\"\n";
            $script .= "echo \"---------------------------\"\n";
            $script .= "        read -r -p \"NKNx FastDeploy also installs UFW by default. Want to remove UFW too? [y/n] \" response\n";
            $script .= "        case \"\$response\" in\n";
            $script .= "            [yY][eE][sS]|[yY])\n";
            $script .= "                ufw disable\n";
            $script .= "                apt-get remove ufw --allow-unauthenticated --allow-remove-essential --allow-downgrades -y\n";
            $script .= "                apt-get purge ufw --allow-unauthenticated --allow-remove-essential --allow-downgrades -y\n";
            $script .= "                ;;\n";
            $script .= "        esac\n";
            $script .= "        ;;\n";
            $script .= "esac\n";
            $script .= "sleep 2\n";
            $script .= "clear\n";
            $script .= "echo\n";
            $script .= "echo\n";
            $script .= "echo\n";
            $script .= "echo\n";
            $script .= "echo \"                                  -----------------------\"\n";
            $script .= "echo \"                                  |  UNINSTALL COMPLETE  |\"\n";
            $script .= "echo \"                                  -----------------------\"\n";
            $script .= "echo\n";
            $script .= "echo \"=============================================================================================\"\n";
            $script .= "echo \"              THANK YOU FOR CHOOSING NKNx FASTDEPLOY - WE HOPE TO SEE YOU SOON!\"\n";
            $script .= "echo \"=============================================================================================\"\n";
            $script .= "echo\n";
            $script .= "echo \"You can now disconnect from your terminal.\"\n";
            $script .= "echo\n";
            $script .= "echo\n";
            $script .= "echo\n";
            $script .= "echo\n";



            return $script;

    }


}
