<template>
  <div v-if="loading">
    <content-spinner show/>
  </div>
  <div v-else class="articles-wrapper">
    <div class="card">
      <div class="card-header d-flex align-items-center">
        <h5 class="mb-0">{{ articles.length }} {{ $t('article(s)') }}</h5>
        <b-button @click="onCreateArticleClick" size="sm" variant="primary" class="ml-auto">
          <i class="fas fa-plus-circle"/>
          {{ $t('Create Article') }}
        </b-button>
      </div>
      <div class="card-body p-0">
        <no-data-available v-if="articles.length === 0" height="100"/>
        <div :class="{'border-bottom': index < articles.length - 1}" v-for="(article, index) in articles"
             :key="`article-item-${article.id}`" class="p-3 d-flex align-items-center">
          <div>
            <router-link class="d-inline-block" :to="{name: 'article.view', params: {id: article.id}}">
              <h5 class="mb-0">{{ article.title }}</h5>
            </router-link>
            <small class="mt-2 text-muted d-block">
              <i class="far fa-clock"></i>
              {{
                $t("Created {time} by {owner}", {
                  time: $options.filters.relativeDate(article.created_at),
                  owner: article.createdBy.displayName
                })
              }}
            </small>
          </div>
          <i class="ml-auto far fa-trash-alt text-danger hover-pointer" @click="onRemoveClicked(article)"></i>
        </div>
      </div>
    </div>

  </div>
</template>

<script>

import {createNamespacedHelpers} from "vuex";
import ContentSpinner from "@/core/components/ContentSpinner";
import NoDataAvailable from "@/core/components/NoDataAvailable";

const {mapState, mapActions} = createNamespacedHelpers('workspace');

export default {
  name: "WorkspaceArticles",
  components: {NoDataAvailable, ContentSpinner},
  computed: {
    ...mapState({
      workspace: state => state.view.workspace,
      articles: state => state.view.articles.data,
      loading: state => state.view.articles.loading,
    })
  },
  methods: {
    ...mapActions(['getArticles', 'showArticleModal', 'deleteArticle']),
    onCreateArticleClick() {
      this.showArticleModal({object: {workspace_id: this.workspace.id}});
    },
    onEditClicked(article) {
      this.showArticleModal({object: article});
    },
    async onRemoveClicked(article) {
      let success = await this.$confirm(this.$i18n.t("Are you sure you want to remove following article?"));
      if (success) {
        let response = await this.deleteArticle(article.id);
        if (response.success) {
          this.$toast(this.$i18n.t("Article was successfully deleted"));
        }
      }
    },
  },
  beforeMount() {
    this.getArticles(this.workspace.id);
  }
}
</script>

<style scoped>
</style>