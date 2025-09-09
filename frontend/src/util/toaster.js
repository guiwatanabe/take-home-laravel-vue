import Vue from "vue";

const toaster = new Vue({
  data: {
    show: false,
    text: "",
    color: "success",
    timeout: 3000,
  },
  methods: {
    open({ text, color = "success", timeout = 3000 }) {
      this.text = text;
      this.color = color;
      this.timeout = timeout;
      this.show = true;
    },
    close() {
      this.show = false;
    },
  },
});

export default toaster;
