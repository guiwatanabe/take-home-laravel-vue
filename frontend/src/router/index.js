import Vue from "vue";
import VueRouter from "vue-router";
import ExamsView from "@/views/ExamsView.vue";

Vue.use(VueRouter);

const routes = [
  {
    path: "/",
    name: "exams",
    component: ExamsView,
  },
];

const router = new VueRouter({
  mode: "history",
  base: process.env.BASE_URL,
  routes,
});

export default router;
