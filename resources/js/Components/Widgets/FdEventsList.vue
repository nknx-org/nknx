<template>
    <Card
        col="12"
        padding="none"
        title="Recent events"
        :counter="`${fd_events.total} in total`"
    >
        <template v-slot:headerControls>
            <Pagination
                routeName="fastdeploy.index"
                :pageData="fd_events"
                pageName="fd_events_page"
            />
        </template>
        <div class="overflow-x">
            <table class="table">
                <thead class="table__header">
                    <tr class="table__row">
                        <th class="table__title" style="width: 1%;">
                            Deployment name
                        </th>
                        <th class="table__title" style="width: 1%;">
                            Server
                        </th>
                        <th class="table__title" style="width: 1%;">
                            Provider
                        </th>
                        <th class="table__title" style="width: 10%;">
                            Event
                        </th>
                        <th
                            class="table__title"
                            style="width: 5%; text-align: right;"
                        >
                            When?
                        </th>
                    </tr>
                </thead>
                <tbody class="table__body">
                    <tr
                        v-for="(event, i) in fd_events.data"
                        :key="i"
                        class="table__row"
                    >
                        <td class="table__item">
                            {{ event.label }}
                        </td>
                        <td class="table__item">
                            {{ event.ip }}
                        </td>
                        <td class="table__item">
                            {{ event.provider }}
                        </td>
                        <td
                            class="table__item font-mono text_wrap_none"
                            style="font-size: 12px; color: #5769df; max-width: 0px;"
                        >
                            {{ getEventName(event.event_code) }}
                        </td>
                        <td class="table__item" style="text-align: right;">
                            {{
                                $moment(event.created_at).format(
                                    "MMM Do, hh:mm:ss a"
                                )
                            }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </Card>
</template>

<script>
import FormMixin from "@/Mixins/FormMixin.js";

import Card from "@/Components/Global/Card";
import Button from "@/Components/Global/Button";
import ModalContent from "@/Components/Global/ModalContent";
import ControlWrapper from "@/Components/Global/ControlWrapper";
import Pagination from "@/Components/Global/Pagination";

export default {
    props: ["fd_events"],
    mixins: [FormMixin],
    components: {
        Card,
        Button,
        ModalContent,
        ControlWrapper,
        Pagination
    },
    data: function() {
        return {};
    },
    computed: {},
    mounted() {},
    methods: {
        getEventName(code) {
            let name = "";

            switch (code) {
                case "11":
                    name = "Installation started";
                    break;
                case "12":
                    name = "Installation finished";
                    break;
                case "13":
                    name = "Sent donation to NKNx";
                    break;
                case "20":
                    name = "Node added to NKNx";
                    break;
                case "30":
                    name = "Downloading chain snapshot";
                    break;
                case "31":
                    name = "Unzipping chain snapshot";
                    break;
                default:
                    return name;
            }

            return name;
        }
    }
};
</script>
