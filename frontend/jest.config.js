module.exports = {
  preset: "@vue/cli-plugin-unit-jest",
  transformIgnorePatterns: ["node_modules/(?!axios)/"],
  setupFiles: ["./tests/setup.js"],
};
