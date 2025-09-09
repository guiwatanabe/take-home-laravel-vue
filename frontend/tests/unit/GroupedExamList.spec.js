import { mount, createLocalVue } from "@vue/test-utils";
import Vuetify from "vuetify";
import GroupedExamList from "@/components/GroupedExamList.vue";

describe("GroupedExamList.vue", () => {
  let wrapper;
  const localVue = createLocalVue();
  let vuetify;

  const mockExams = [
    {
      id: 1,
      name: "Exame A",
      laterality: null,
      comment: "",
      group: "Individual",
    },
    {
      id: 2,
      name: "Exame B",
      laterality: null,
      comment: "",
      group: "Individual",
    },
  ];

  beforeEach(() => {
    vuetify = new Vuetify();
    wrapper = mount(GroupedExamList, {
      localVue,
      vuetify,
      propsData: {
        packageId: 1,
        groupName: "Pacote 1",
        exams: [...mockExams],
      },
    });
  });

  it("renders the group name", () => {
    expect(wrapper.find({ ref: "groupNameTitle" }).text()).toBe("Pacote 1");
  });

  it("emits remove-group with packageId when the remove button is clicked", async () => {
    wrapper.find({ ref: "removePackageButton" }).trigger("click");
    await wrapper.vm.$nextTick();
    expect(wrapper.emitted("remove-group")).toBeTruthy();
    expect(wrapper.emitted("remove-group")[0][0]).toBe(1);
  });

  it("emits change-package-group when the package select changes", async () => {
    wrapper.vm.changeGroupsGroup("Grupo 2");
    await wrapper.vm.$nextTick();
    expect(wrapper.emitted("change-package-group")).toBeTruthy();
    expect(wrapper.emitted("change-package-group")[0][0]).toEqual({
      packageId: 1,
      newGroup: "Grupo 2",
    });
  });

  it("emits change-group when an exam group changes", async () => {
    const exam = { ...mockExams[0], group: "Grupo 1" };
    wrapper.vm.changeGroup(exam);
    await wrapper.vm.$nextTick();
    expect(wrapper.emitted("change-group")).toBeTruthy();
    expect(wrapper.emitted("change-group")[0][0]).toEqual({
      packageId: 1,
      examId: exam.id,
      newGroup: "Grupo 1",
    });
  });

  it("emits remove-exam when the trash icon is clicked", async () => {
    const exam = mockExams[0];
    wrapper.vm.removeExam(exam);
    await wrapper.vm.$nextTick();
    expect(wrapper.emitted("remove-exam")).toBeTruthy();
    expect(wrapper.emitted("remove-exam")[0][0]).toEqual({
      packageId: 1,
      examId: exam.id,
    });
  });

  it("updates observations and emits update-observations", async () => {
    wrapper.setData({ observations: "Nova observação" });
    wrapper.vm.updateObservations();
    await wrapper.vm.$nextTick();
    expect(wrapper.emitted("update-observations")).toBeTruthy();
    expect(wrapper.emitted("update-observations")[0][0]).toEqual({
      packageId: 1,
      observations: "Nova observação",
    });
  });

  it("updates exam laterality and comment correctly", async () => {
    const exam = wrapper.props().exams[0];
    wrapper.setData({
      exams: [{ ...exam, laterality: "OD", comment: "Teste" }],
    });
    expect(wrapper.vm.exams[0].laterality).toBe("OD");
    expect(wrapper.vm.exams[0].comment).toBe("Teste");
  });
});
