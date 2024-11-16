<script setup>
import Fluid from 'primevue/fluid';
import { ref } from 'vue';

const requestingFacilityValue = ref('');
const addressValue = ref('');
const pathologistValue = ref('');
const facilityTransacNumValue = ref('');
const requisitionItems = ref([
    {
        bloodComponent: '',
        bloodType: '',
        quantity: ''
    }
]);

function addRequisitionItem() {
    requisitionItems.value.push({
        bloodComponent: '',
        bloodType: '',
        quantity: ''
    });
}
function removeRequisitionItem(index) {
    requisitionItems.value.splice(index, 1);
}
</script>
<template>
    <div class="font-bold text-center text-xl mb-6">Blood Request Inquisition Slip Form (RIS)</div>
    <div class="card">
        <span class="font-semibold mb-6 block">Basic Details</span>
        <Fluid>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <FloatLabel>
                        <InputText id="requestingFacility" type="text" v-model="requestingFacilityValue" />
                        <label for="requestingFacility">Requesting Facility / Hospital</label>
                    </FloatLabel>
                </div>
                <div>
                    <FloatLabel>
                        <InputText id="address" type="text" v-model="addressValue" />
                        <label for="address">Address</label>
                    </FloatLabel>
                </div>
                <div class="mt-3">
                    <FloatLabel>
                        <InputText class="" id="pathologist" type="text" v-model="pathologistValue" />
                        <label for="pathologist">Pathologist</label>
                    </FloatLabel>
                </div>
                <div class="mt-3">
                    <FloatLabel>
                        <InputText id="facilityTransacNum" type="number" v-model="facilityTransacNumValue" />
                        <label for="facilityTransacNum">Facility / Hospital Transaction Number #</label>
                    </FloatLabel>
                </div>
            </div>
        </Fluid>
    </div>
    <span class="font-bold mb-6 block">Blood Inventory Report (In-Stock)</span>
    <div class="grid grid-cols-2 gap-4">
        <div class="card" style="height: 375px">
            <span class="font-semibold mb-6 block text-center">Uncrossmatched (Quantity)</span>
            <hr class="mb-3" />
            <Fluid>
                <div class="grid grid-cols-2 gap-4">
                    <span class="font text-center text-muted-color">Whole Blood</span>
                    <span class="font text-center text-muted-color">Packed RBC</span>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">A+</label>
                            </FloatLabel>
                        </div>
                        <div>
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">A-</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">B+</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">B-</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">O+</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">O-</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">AB+</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">AB-</label>
                            </FloatLabel>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">A+</label>
                            </FloatLabel>
                        </div>
                        <div>
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">A-</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">B+</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">B-</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">O+</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">O-</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">AB+</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">AB-</label>
                            </FloatLabel>
                        </div>
                    </div>
                </div>
            </Fluid>
        </div>
        <div class="card" style="height: 375px">
            <span class="font-semibold mb-6 block text-center">Crossmatched (Quantity)</span>
            <hr class="mb-3" />
            <Fluid>
                <div class="grid grid-cols-2 gap-4">
                    <span class="font text-center text-muted-color">Whole Blood</span>
                    <span class="font text-center text-muted-color">Packed RBC</span>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">A+</label>
                            </FloatLabel>
                        </div>
                        <div>
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">A-</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">B+</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">B-</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">O+</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">O-</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">AB+</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">AB-</label>
                            </FloatLabel>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">A+</label>
                            </FloatLabel>
                        </div>
                        <div>
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">A-</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">B+</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">B-</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">O+</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">O-</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">AB+</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="" type="number"></InputText>
                                <label for="">AB-</label>
                            </FloatLabel>
                        </div>
                    </div>
                </div>
            </Fluid>
        </div>
    </div>
    <span class="font-bold mb-6 block">Requisition</span>
    <div class="grid grid-cols-2 gap-4">
        <div class="card p-4">
            <div v-for="(item, index) in requisitionItems" :key="index">
                <div class="mb-6">
                    <span class="">Request #{{ index + 1 }}</span>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText type="text" v-model="item.bloodComponent" class="w-full" />
                            <label for="">Blood Component</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText type="text" v-model="item.bloodType" class="w-full" />
                            <label for="">Blood Type</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText type="number" v-model="item.quantity" class="w-full" />
                            <label for="">Quantity</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-2">
                        <FloatLabel>
                            <InputText type="text" v-model="item.remarks" class="w-full" />
                            <label for="">Remarks</label>
                        </FloatLabel>
                    </div>
                    <div class="flex justify-end">
                        <Button @click="removeRequisitionItem(index)" class="p-button-danger w-full">Remove<i class="pi pi-trash text-white-500"></i></Button>
                    </div>
                </div>
                <hr class="mt-4 mb-5" />
            </div>
            <div class="flex justify-center mb-4">
                <Button @click="addRequisitionItem" class="w-full">Add <i class="pi pi-plus text-white-500"></i></Button>
            </div>
        </div>
    </div>
</template>
