// Imports
import { shallowMount, createLocalVue } from "@vue/test-utils";
import EditExamPackageModal from "@/components/EditExamPackageModal.vue";
import Vuetify from "vuetify";
import api from "@/util/api";
import toaster from "@/util/toaster";

jest.mock("@/util/api");
jest.mock("@/util/toaster");

describe("EditExamPackageModal.vue", () => {
  let wrapper;
  const mockExams = [
    { id: 1, name: "Exame A" },
    { id: 2, name: "Exame B" },
  ];
  const newPackageData = {
    name: "Novo Pacote",
    exams: [{ id: 1, name: "Exame A" }],
    observations: "Obs do Novo Pacote",
  };
  const existingPackageData = {
    id: 1,
    name: "Pacote Existente",
    exams: [],
    observations: "Obs do Pacote Existente",
  };

  const localVue = createLocalVue();
  let vuetify;

  beforeEach(() => {
    vuetify = new Vuetify();
    jest.clearAllMocks();

    api.post.mockResolvedValue({ data: newPackageData });
    api.put.mockResolvedValue({ data: existingPackageData });

    wrapper = shallowMount(EditExamPackageModal, {
      localVue,
      vuetify,
      propsData: {
        value: true,
        exams: mockExams,
        examPackage: {
          id: null,
          name: "",
          exams: [],
          observations: "",
        },
      },
    });
  });

  it("should set localExamPackage when the value prop changes", async () => {
    wrapper.setProps({ value: true, examPackage: existingPackageData });
    await wrapper.vm.$nextTick();
    expect(wrapper.vm.localExamPackage).toEqual(existingPackageData);
  });

  it("should correctly add an exam to the package", async () => {
    wrapper.vm.addExamToPackage(mockExams[0]);
    await wrapper.vm.$nextTick();
    expect(wrapper.vm.localExamPackage.exams).toEqual([mockExams[0]]);
  });

  it("should not add a duplicate exam", async () => {
    wrapper.vm.addExamToPackage(mockExams[0]);
    wrapper.vm.addExamToPackage(mockExams[0]);
    await wrapper.vm.$nextTick();
    expect(wrapper.vm.localExamPackage.exams).toHaveLength(1);
  });

  it("should correctly remove an exam from the package", async () => {
    wrapper.vm.localExamPackage.exams = [...mockExams];
    wrapper.vm.removeExam(0);
    await wrapper.vm.$nextTick();
    expect(wrapper.vm.localExamPackage.exams).toEqual([mockExams[1]]);
  });

  it("should create a new package on save", async () => {
    wrapper.vm.localExamPackage = newPackageData;
    await wrapper.vm.saveExamPackage();
    expect(api.post).toHaveBeenCalledWith(
      "packages",
      expect.objectContaining({
        name: "Novo Pacote",
      })
    );
    expect(toaster.open).toHaveBeenCalledWith({
      color: "success",
      text: "Pacote Novo Pacote criado com sucesso.",
    });
    expect(wrapper.emitted("package-created")).toBeTruthy();
    expect(wrapper.emitted("input")).toBeTruthy();
    expect(wrapper.emitted("input")[0][0]).toBe(false);
  });

  it("should update an existing package on save", async () => {
    wrapper.vm.localExamPackage = {
      ...existingPackageData,
      name: "Pacote Editado",
    };
    await wrapper.vm.saveExamPackage();
    expect(api.put).toHaveBeenCalledWith(
      "packages/1",
      expect.objectContaining({
        name: "Pacote Editado",
      })
    );
    expect(toaster.open).toHaveBeenCalledWith({
      color: "success",
      text: "Pacote Pacote Existente salvo com sucesso.",
    });
    expect(wrapper.emitted("package-updated")).toBeTruthy();
    expect(wrapper.emitted("input")).toBeTruthy();
  });

  it("should handle validation errors from the API", async () => {
    const mockErrors = { name: ["O campo nome é obrigatório."] };
    api.post.mockRejectedValue({
      response: {
        status: 422,
        data: { errors: mockErrors },
      },
    });
    await wrapper.vm.saveExamPackage();
    expect(wrapper.vm.validationErrors).toEqual(mockErrors);
    expect(toaster.open).not.toHaveBeenCalled();
    expect(wrapper.emitted("package-created")).toBeFalsy();
  });

  it("should handle general API errors", async () => {
    api.post.mockRejectedValue({
      response: {
        status: 500,
      },
    });
    await wrapper.vm.saveExamPackage();
    expect(toaster.open).toHaveBeenCalledWith({
      color: "error",
      text: "Erro ao salvar o pacote de exames.",
    });
    expect(wrapper.emitted("package-created")).toBeFalsy();
  });
});
