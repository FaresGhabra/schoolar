<template>
  <!--begin::App-->
  <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
    <!--begin::Page-->
    <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
      <KTHeader />
      <!--begin::Wrapper-->
      <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
        <KTSidebar />
        <!--begin::Main-->
        <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
          <!--begin::Content wrapper-->
          <div class="d-flex flex-column flex-column-fluid">
            <KTToolbar />
            <div id="kt_app_content" class="app-content flex-column-fluid">
              <KTContent></KTContent>
            </div>
          </div>
          <!--end::Content wrapper-->
          <KTFooter />
        </div>
        <!--end:::Main-->
      </div>
      <!--end::Wrapper-->
    </div>
    <!--end::Page-->
  </div>
  <!--end::App-->

  <KTDrawers />
  <KTScrollTop />
  <KTModals />
  <KTCustomize />
</template>

<script lang="ts">
import { defineComponent, nextTick, onBeforeMount, onMounted, watch } from 'vue';
import KTHeader from './header/Header.vue';
import KTSidebar from './sidebar/Sidebar.vue';
import KTContent from './content/Content.vue';
import KTToolbar from './toolbar/Toolbar.vue';
import KTFooter from './footer/Footer.vue';
import KTDrawers from './drawers/Drawers.vue';
import KTModals from './modals/Modals.vue';
import KTScrollTop from './extras/ScrollTop.vue';
import KTCustomize from './extras/Customize.vue';
import { useRoute } from 'vue-router';
import { reinitializeComponents } from '../../core/plugins/keenthemes';
import LayoutService from '../../core/services/LayoutService';

export default defineComponent({
  name: 'default-layout',
  components: {
    KTHeader,
    KTSidebar,
    KTContent,
    KTToolbar,
    KTFooter,
    KTDrawers,
    KTScrollTop,
    KTModals,
    KTCustomize,
  },
  setup() {
    const route = useRoute();

    onBeforeMount(() => {
      LayoutService.init();
    });

    onMounted(() => {
      nextTick(() => {
        reinitializeComponents();
      });
    });

    watch(
      () => route.path,
      () => {
        nextTick(() => {
          reinitializeComponents();
        });
      },
    );
  },
});
</script>
