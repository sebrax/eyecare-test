import Vue from "vue";
import Router from "vue-router";
Vue.use(Router);

export default new Router({
  mode: "history",
  routes: [
    { path: "/", redirect: "/solicitacao" },
    { path: "/exames", component: () => import("@/views/Exam/ExameList.vue") },
    {
      path: "/solicitacao",
      component: () => import("@/views/Exam/ExamRequest.vue"),
    },
  ],
});
