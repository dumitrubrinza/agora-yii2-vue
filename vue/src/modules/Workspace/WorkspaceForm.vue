<template>
  <ValidationObserver ref="form" v-slot="{ handleSubmit, invalid ,reset}">
    <b-modal
      :visible="showModal" id="workspace-form" ref="modal" size="lg"
      :title='modalWorkspace ? $t(`Update workspace "{workspace}"`,{workspace:model.name}) : $t(`Add New Workspace`)'
      @hidden="hideModal" @ok.prevent="handleSubmit(onSubmit)" :ok-title="$t('Submit')"
      scrollable>
      <b-form @submit.prevent="handleSubmit(onSubmit)" novalidate>
        <input-widget
          :model="model" attribute="image" type="file" :placeholder="$t('Choose a image or drop it here...')">
        </input-widget>
        <input-widget :model="model" attribute="name"/>
        <input-widget :model="model" attribute="abbreviation"/>
        <input-widget :model="model" attribute="description" type="richtext"/>
      </b-form>
    </b-modal>
  </ValidationObserver>
</template>

<script>
import WorkspaceFormModel from "./WorkspaceFormModel";
import InputWidget from "../../core/components/input-widget/InputWidget";
import {createNamespacedHelpers} from "vuex";

const {mapState, mapActions} = createNamespacedHelpers('workspace');

export default {
  name: "WorkspaceForm",
  components: {InputWidget},
  data() {
    return {
      model: new WorkspaceFormModel(),
    }
  },
  computed: {
    ...mapState(['showModal', 'modalWorkspace']),
  },
  watch: {
    modalWorkspace() {
      if (this.modalWorkspace) {
        this.model = new WorkspaceFormModel(this.modalWorkspace);
      }
    }
  },
  methods: {
    ...mapActions(['hideWorkspaceModal', 'createWorkspace', 'updateWorkspace']),
    async onSubmit() {
      let action
      let res
      if (this.model.id) {
        action = 'updated';
        res = await this.updateWorkspace({...this.model.toJSON()});
      } else {
        action = 'created';
        res = await this.createWorkspace({...this.model.toJSON()});
      }
      if (res.success) {
        this.$toast(this.$t(`The workspace '{name}' was successfully ${action}`, {name: this.model.name}));
        this.hideModal()
      } else {
        this.model.setMultipleErrors(res.body);
      }
    },
    hideModal() {
      this.hideWorkspaceModal();
      this.model = new WorkspaceFormModel()
    }
  }
}
</script>

<style lang="scss" scoped>

</style>