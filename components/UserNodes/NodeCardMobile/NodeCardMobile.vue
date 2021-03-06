<template>
  <Card col="12" :overflow="false">
    <div class="node-card-mobile">
      <div class="node-card-mobile__header">
        <div class="node-card-mobile__label">
          <span v-if="node.pivot.label"> {{ node.pivot.label }} </span>
          <span v-else>{{ $t('node') }}</span>
        </div>
        <span
          v-on-clickaway="closeActionsModal"
          class="node-manager__actions fe fe-more-horizontal"
          @click="isActions = node.id"
        >
          <div
            :class="[
              'node-manager__actions-modal node-manager__actions-modal_mobile',
              isActions === node.id
                ? 'node-manager__actions-modal_visible'
                : null
            ]"
          >
            <div
              class="node-manager__actions-item"
              @click="openEditNodeModal(node)"
            >
              <span class="node-manager__actions-icon fe fe-edit-2"></span>
              <span class="node-manager__actions-title">{{
                $t('editNode')
              }}</span>
            </div>
            <div
              class="node-manager__actions-item"
              @click="openDeleteNodeModal(node)"
            >
              <span class="node-manager__actions-icon fe fe-trash-2"></span>
              <span class="node-manager__actions-title">{{
                $t('delete')
              }}</span>
            </div>
          </div>
        </span>
      </div>
      <div class="node-card-mobile__body">
        <div class="node-card-mobile__addr">{{ node.addr }}</div>
        <div
          v-clipboard:copy="node.addr"
          class="node-card-mobile__copy"
          @click="copyAlert"
        >
          {{ $t('copy') }}
        </div>
      </div>
      <div class="node-card-mobile__footer">
        <NodeStatus :status="node.syncState" />
        <div class="node-card-mobile__stats">
          <span class="node-card-mobile__icon fe fe-git-branch"></span>
          <span v-if="node.version !== null">{{
            node.version | nodeVersion
          }}</span>
          <span v-else>{{ $t('n/a') }}</span>
        </div>
        <div class="node-card-mobile__stats">
          <span class="node-card-mobile__icon fe fe-box"></span>
          <span v-if="node.version !== null">{{ node.height }}</span
          ><span v-else>{{ $t('n/a') }}</span>
        </div>
        <div class="node-card-mobile__stats">
          <span class="node-card-mobile__icon fe fe-gift"></span>
          <span v-if="node.node_snapshots.length">{{
            node.node_snapshots[0].mined
          }}</span>
          <span v-else>{{ $t('n/a') }}</span>
        </div>
      </div>
    </div>
  </Card>
</template>

<style lang="scss">
@import './NodeCardMobile.scss';
</style>

<script>
import { mapGetters } from 'vuex'
import { mixin as clickaway } from 'vue-clickaway'

import NodeStatus from '~/components/UserNodes/NodeStatus/NodeStatus.vue'
import Card from '~/components/Card/Card.vue'

export default {
  components: { NodeStatus, Card },
  mixins: [clickaway],
  props: {
    node: {
      type: Object,
      default: () => {}
    }
  },
  data: () => {
    return {
      headings: [
        { value: 'addr', title: 'ipAddress' },
        { value: 'node_user.label', title: 'name' },
        { value: '', title: 'status' },
        { value: '', title: 'latestBlock' },
        { value: '', title: 'currentVersion' },
        { value: 'blocksMined', title: 'blocksMined' },
        { value: 'relayMessageCount', title: 'relayedMessages' },
        { value: '', title: 'miningHistory' },
        { value: '', title: 'actions' }
      ],
      selected: [],
      isAll: false,
      active: 'relayMessageCount',
      order: false,
      isActions: false,
      loaderCount: 10
    }
  },
  computed: {
    ...mapGetters({
      userConfig: 'userNodes/getUserConfig'
    })
  },
  mounted: function() {},
  methods: {
    closeActionsModal() {
      this.isActions = false
    },
    openDeleteNodeModal(node) {
      this.$store.dispatch('activeNode/updateActiveNode', node)
      this.$store.dispatch('modals/updateDeleteNodeModalVisible', true)
      setTimeout(this.closeActionsModal, 1)
    },
    openEditNodeModal(node) {
      this.$store.dispatch('activeNode/updateActiveNode', node)
      this.$store.dispatch('modals/updateEditNodeModalVisible', true)
      setTimeout(this.closeActionsModal, 1)
    },
    copyAlert() {
      this.$store.dispatch('snackbar/updateSnack', {
        snack: 'nodeIpCopyAlert',
        color: 'alert',
        timeout: true
      })
    }
  }
}
</script>
