<template>
  <div class="modal-wrapper">
    <div class="modal-dialog">
      <div v-on-clickaway="closeModal" class="modal-form">
        <div class="modal__header">
          <div class="modal__heading">{{ $t('nodeManager') }}</div>
          <span class="modal__close fe fe-x" @click="closeModal"></span>
        </div>
        <div class="modal__title">{{ $t('deleteAllNodes') }}</div>
        <div class="modal__body modal__body_wrap">
          <div class="modal__message">{{ $t('deleteAllNodesConfirm') }}</div>
          <div
            :class="[
              'modal-input',
              isError === true || isInvalid === true
                ? 'modal-input_error'
                : isSuccess === true
                ? 'modal-input_success'
                : null
            ]"
          >
            <div class="modal-input__alert">{{ $t(alertMsg) }}</div>
          </div>
        </div>
        <div class="modal__footer">
          <span
            :class="[
              'modal__footer-loader fe fe-loader',
              isLoading === true ? 'modal__footer-loader_visible' : null
            ]"
          ></span>
          <Button
            class="modal__footer-button"
            type="button"
            theme="white"
            @click.native="closeModal"
            >{{ $t('cancel') }}</Button
          >
          <Button
            class="modal__footer-button"
            type="button"
            theme="primary"
            @click.native="deleteAllNodes"
            >{{ $t('confirm') }}</Button
          >
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss">
@import './DeleteAllNodesModal.scss';
</style>

<script>
import { mapGetters } from 'vuex'
import { mixin as clickaway } from 'vue-clickaway'
import Button from '~/components/Button/Button.vue'

export default {
  components: { Button },
  mixins: [clickaway],
  data: () => {
    return {
      address: '',
      label: '',
      isError: false,
      isSuccess: false,
      alertMsg: '',
      isLoading: false,
      isInvalid: false
    }
  },
  computed: {
    ...mapGetters({
      deleteAllNodesModalVisible: 'modals/getDeleteAllNodesModalVisible'
    })
  },
  destroyed() {},
  created: function() {},
  mounted: function() {},
  methods: {
    closeModal() {
      this.$store.dispatch('modals/updateDeleteAllNodesModalVisible', false)
    },
    deleteAllNodes() {
      const self = this
      this.isLoading = true
      this.$axios
        .$delete(`nodes`)
        .then(response => {
          self.alertMsg = 'successAllNodesDeleteAlert'
          self.isSuccess = true
          self.$store.dispatch('userNodes/updateUserNodes')
          self.isLoading = false
          self.closeModal()
          this.$store.dispatch('snackbar/updateSnack', {
            snack: 'successAllNodesDeleteAlert',
            color: 'success',
            timeout: true
          })
        })
        .catch(error => {
          self.isError = true
          self.alertMsg = 'failedAllNodesDeleteAlert'
          self.isLoading = false
          self.closeModal()
          this.$store.dispatch('snackbar/updateSnack', {
            snack: 'failedAllNodesDeleteAlert',
            color: 'error',
            timeout: true
          })
        })
    }
  }
}
</script>
