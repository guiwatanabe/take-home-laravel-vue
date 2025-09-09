// Imports
import { mount, createLocalVue } from "@vue/test-utils";
import ExamPackageModal from "@/components/ExamPackageModal.vue";
import Vuetify from "vuetify";
import api from "@/util/api";
import toaster from "@/util/toaster";

jest.mock("@/util/api");
jest.mock("@/util/toaster");

describe("ExamPackageModal.vue", () => {
  let wrapper;
  const mockPackages = [
    { id: 1, name: "Pacote A", observations: "Obs A", exams: [] },
    { id: 2, name: "Pacote B", observations: "Obs B", exams: [] },
  ];

  const localVue = createLocalVue();
  let vuetify;

  beforeEach(() => {
    vuetify = new Vuetify();

    jest.clearAllMocks();

    api.get.mockResolvedValue({ data: { data: mockPackages } });
    api.delete.mockResolvedValue({ status: 200, data: { name: "Pacote A" } });

    wrapper = mount(ExamPackageModal, {
      localVue,
      vuetify,
      propsData: { value: true, exams: [] },
    });
  });

  it("should fetch packages on mount and set loading to false", async () => {
    await wrapper.vm.$nextTick(); // wait for async mounted hook
    expect(api.get).toHaveBeenCalledWith("packages");
    expect(wrapper.vm.examPackages).toEqual(mockPackages);
    expect(wrapper.vm.isLoading).toBe(false);
  });

  it("should add the selected package and emit an event", async () => {
    wrapper.vm.selectedExamPackages = [mockPackages[0]];
    wrapper.vm.addPackage(wrapper.vm.selectedExamPackages);
    await wrapper.vm.$nextTick();
    expect(wrapper.emitted("add-package")).toBeTruthy();
    expect(wrapper.emitted("add-package")[0][0]).toEqual(mockPackages[0]);
    expect(wrapper.vm.selectedExamPackages).toHaveLength(0);
  });

  it("shows a warning if no package is selected", async () => {
    wrapper.vm.selectedExamPackages = [];
    wrapper.vm.addPackage([]);
    await wrapper.vm.$nextTick();

    expect(wrapper.emitted("add-package")).toBeFalsy();
    expect(toaster.open).toHaveBeenCalledWith({
      color: "warning",
      text: "Selecione um pacote para adicionar.",
    });
  });

  it("should delete an item and show a success message", async () => {
    await wrapper.vm.deleteItem(mockPackages[0]);
    expect(api.delete).toHaveBeenCalledWith("packages/1");
    expect(toaster.open).toHaveBeenCalledWith({
      color: "success",
      text: "Pacote Pacote A excluído.",
    });
    expect(wrapper.emitted("package-deleted")).toBeTruthy();
  });

  it("should show a warning toaster if no package is selected when adding", () => {
    wrapper.vm.selectedExamPackages = [];
    wrapper.vm.addPackage(wrapper.vm.selectedExamPackages);
    expect(wrapper.emitted("add-package")).toBeFalsy();
    expect(toaster.open).toHaveBeenCalledWith({
      color: "warning",
      text: "Selecione um pacote para adicionar.",
    });
  });

  it("handles API errors on delete", async () => {
    api.delete.mockRejectedValue({ response: { status: 500 } });
    await wrapper.vm.deleteItem(mockPackages[0]);

    expect(toaster.open).toHaveBeenCalledWith({
      color: "error",
      text: "Erro ao excluir pacote.",
    });
    expect(wrapper.emitted("package-deleted")).toBeFalsy();
  });
});
