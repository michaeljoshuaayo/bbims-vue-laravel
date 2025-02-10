<script setup>
import Fluid from 'primevue/fluid';
import { ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import axios from 'axios'; 
import api from '@/services/api'; 
import Dropdown from 'primevue/dropdown';

const toast = useToast();
const requestingFacilityValue = ref('');
const addressValue = ref('');
const pathologistValue = ref('');
const facilityTransacNumValue = ref('');
const requestedByValue = ref('');
const requisitionItems = ref([
    {
        bloodComponent: '',
        bloodType: '',
        quantity: '',
        remarks: ''
    }
]);
const bloodTypes = [
    { label: 'A+', value: 'A+' },
    { label: 'A-', value: 'A-' },
    { label: 'B+', value: 'B+' },
    { label: 'B-', value: 'B-' },
    { label: 'O+', value: 'O+' },
    { label: 'O-', value: 'O-' },
    { label: 'AB+', value: 'AB+' },
    { label: 'AB-', value: 'AB-' }
];
const uncrossmatchedQuantities = ref({
    wholeBlood: {
        APlus: '',
        AMinus: '',
        BPlus: '',
        BMinus: '',
        OPlus: '',
        OMinus: '',
        ABPlus: '',
        ABMinus: ''
    },
    packedRBC: {
        APlus: '',
        AMinus: '',
        BPlus: '',
        BMinus: '',
        OPlus: '',
        OMinus: '',
        ABPlus: '',
        ABMinus: ''
    }
});

const crossmatchedQuantities = ref({
    wholeBlood: {
        APlus: '',
        AMinus: '',
        BPlus: '',
        BMinus: '',
        OPlus: '',
        OMinus: '',
        ABPlus: '',
        ABMinus: ''
    },
    packedRBC: {
        APlus: '',
        AMinus: '',
        BPlus: '',
        BMinus: '',
        OPlus: '',
        OMinus: '',
        ABPlus: '',
        ABMinus: ''
    }
});

function addRequisitionItem() {
    requisitionItems.value.push({
        bloodComponent: '',
        bloodType: '',
        quantity: '',
        remarks: ''
    });
}
function removeRequisitionItem(index) {
    requisitionItems.value.splice(index, 1);
}

async function submitForm() {
    const formData = {
        requestingFacility: requestingFacilityValue.value,
        address: addressValue.value,
        pathologist: pathologistValue.value,
        facilityTransacNum: facilityTransacNumValue.value,
        requestedBy: requestedByValue.value,
        requisitionItems: requisitionItems.value,
        uncrossmatchedQuantities: uncrossmatchedQuantities.value,
        crossmatchedQuantities: crossmatchedQuantities.value,
    };

    // Log the form data to the console
    console.log('Form Data:', formData);

    try {
        const response = await api.post('/request-inquisition-slip', formData);
        console.log('Form submitted successfully', response.data);
        toast.add({ severity: 'success', summary: 'Successful', detail: 'Form submitted successfully', life: 3000 });

        // Clear the inputs
        requestingFacilityValue.value = '';
        addressValue.value = '';
        pathologistValue.value = '';
        facilityTransacNumValue.value = '';
        requestedByValue.value = '';
        requisitionItems.value = [
            {
                bloodComponent: '',
                bloodType: '',
                quantity: '',
                remarks: ''
            }
        ];
        uncrossmatchedQuantities.value = {
            wholeBlood: {
                APlus: '',
                AMinus: '',
                BPlus: '',
                BMinus: '',
                OPlus: '',
                OMinus: '',
                ABPlus: '',
                ABMinus: ''
            },
            packedRBC: {
                APlus: '',
                AMinus: '',
                BPlus: '',
                BMinus: '',
                OPlus: '',
                OMinus: '',
                ABPlus: '',
                ABMinus: ''
            }
        };
        crossmatchedQuantities.value = {
            wholeBlood: {
                APlus: '',
                AMinus: '',
                BPlus: '',
                BMinus: '',
                OPlus: '',
                OMinus: '',
                ABPlus: '',
                ABMinus: ''
            },
            packedRBC: {
                APlus: '',
                AMinus: '',
                BPlus: '',
                BMinus: '',
                OPlus: '',
                OMinus: '',
                ABPlus: '',
                ABMinus: ''
            }
        };
    } catch (error) {
        console.error('Error submitting form', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to submit form', life: 3000 });
    }
}
</script>
<template>
    <div class="font-bold text-center text-xl mb-6">Blood Request Inquisition Slip Form (RIS)</div>
    <div class="card">
        <span class="font-semibold mb-6 block">Basic Details <span class="font text-red-500 text-sm">*All fields required*</span>
    </span>
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
                <div class="mt-3">
                    <FloatLabel>
                        <InputText id="requestedBy" type="text" v-model="requestedByValue" />
                        <label for="requestedBy">Requested By</label>
                    </FloatLabel>
                </div>
            </div>
        </Fluid>
    </div>
    <span class="font-bold mb-6 block">Blood Inventory Report (In-Stock) <span class="font-light">*Optional*</span></span>
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
                                <InputText id="uncrossmatchedAPlus" type="number" v-model="uncrossmatchedQuantities.wholeBlood.APlus" />
                                <label for="uncrossmatchedAPlus">A+</label>
                            </FloatLabel>
                        </div>
                        <div>
                            <FloatLabel>
                                <InputText id="uncrossmatchedAMinus" type="number" v-model="uncrossmatchedQuantities.wholeBlood.AMinus" />
                                <label for="uncrossmatchedAMinus">A-</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="uncrossmatchedBPlus" type="number" v-model="uncrossmatchedQuantities.wholeBlood.BPlus" />
                                <label for="uncrossmatchedBPlus">B+</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="uncrossmatchedBMinus" type="number" v-model="uncrossmatchedQuantities.wholeBlood.BMinus" />
                                <label for="uncrossmatchedBMinus">B-</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="uncrossmatchedOPlus" type="number" v-model="uncrossmatchedQuantities.wholeBlood.OPlus" />
                                <label for="uncrossmatchedOPlus">O+</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="uncrossmatchedOMinus" type="number" v-model="uncrossmatchedQuantities.wholeBlood.OMinus" />
                                <label for="uncrossmatchedOMinus">O-</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="uncrossmatchedABPlus" type="number" v-model="uncrossmatchedQuantities.wholeBlood.ABPlus" />
                                <label for="uncrossmatchedABPlus">AB+</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="uncrossmatchedABMinus" type="number" v-model="uncrossmatchedQuantities.wholeBlood.ABMinus" />
                                <label for="uncrossmatchedABMinus">AB-</label>
                            </FloatLabel>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <FloatLabel>
                                <InputText id="uncrossmatchedAPlusPacked" type="number" v-model="uncrossmatchedQuantities.packedRBC.APlus" />
                                <label for="uncrossmatchedAPlusPacked">A+</label>
                            </FloatLabel>
                        </div>
                        <div>
                            <FloatLabel>
                                <InputText id="uncrossmatchedAMinusPacked" type="number" v-model="uncrossmatchedQuantities.packedRBC.AMinus" />
                                <label for="uncrossmatchedAMinusPacked">A-</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="uncrossmatchedBPlusPacked" type="number" v-model="uncrossmatchedQuantities.packedRBC.BPlus" />
                                <label for="uncrossmatchedBPlusPacked">B+</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="uncrossmatchedBMinusPacked" type="number" v-model="uncrossmatchedQuantities.packedRBC.BMinus" />
                                <label for="uncrossmatchedBMinusPacked">B-</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="uncrossmatchedOPlusPacked" type="number" v-model="uncrossmatchedQuantities.packedRBC.OPlus" />
                                <label for="uncrossmatchedOPlusPacked">O+</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="uncrossmatchedOMinusPacked" type="number" v-model="uncrossmatchedQuantities.packedRBC.OMinus" />
                                <label for="uncrossmatchedOMinusPacked">O-</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="uncrossmatchedABPlusPacked" type="number" v-model="uncrossmatchedQuantities.packedRBC.ABPlus" />
                                <label for="uncrossmatchedABPlusPacked">AB+</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="uncrossmatchedABMinusPacked" type="number" v-model="uncrossmatchedQuantities.packedRBC.ABMinus" />
                                <label for="uncrossmatchedABMinusPacked">AB-</label>
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
                                <InputText id="crossmatchedAPlus" type="number" v-model="crossmatchedQuantities.wholeBlood.APlus" />
                                <label for="crossmatchedAPlus">A+</label>
                            </FloatLabel>
                        </div>
                        <div>
                            <FloatLabel>
                                <InputText id="crossmatchedAMinus" type="number" v-model="crossmatchedQuantities.wholeBlood.AMinus" />
                                <label for="crossmatchedAMinus">A-</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="crossmatchedBPlus" type="number" v-model="crossmatchedQuantities.wholeBlood.BPlus" />
                                <label for="crossmatchedBPlus">B+</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="crossmatchedBMinus" type="number" v-model="crossmatchedQuantities.wholeBlood.BMinus" />
                                <label for="crossmatchedBMinus">B-</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="crossmatchedOPlus" type="number" v-model="crossmatchedQuantities.wholeBlood.OPlus" />
                                <label for="crossmatchedOPlus">O+</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="crossmatchedOMinus" type="number" v-model="crossmatchedQuantities.wholeBlood.OMinus" />
                                <label for="crossmatchedOMinus">O-</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="crossmatchedABPlus" type="number" v-model="crossmatchedQuantities.wholeBlood.ABPlus" />
                                <label for="crossmatchedABPlus">AB+</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="crossmatchedABMinus" type="number" v-model="crossmatchedQuantities.wholeBlood.ABMinus" />
                                <label for="crossmatchedABMinus">AB-</label>
                            </FloatLabel>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <FloatLabel>
                                <InputText id="crossmatchedAPlusPacked" type="number" v-model="crossmatchedQuantities.packedRBC.APlus" />
                                <label for="crossmatchedAPlusPacked">A+</label>
                            </FloatLabel>
                        </div>
                        <div>
                            <FloatLabel>
                                <InputText id="crossmatchedAMinusPacked" type="number" v-model="crossmatchedQuantities.packedRBC.AMinus" />
                                <label for="crossmatchedAMinusPacked">A-</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="crossmatchedBPlusPacked" type="number" v-model="crossmatchedQuantities.packedRBC.BPlus" />
                                <label for="crossmatchedBPlusPacked">B+</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="crossmatchedBMinusPacked" type="number" v-model="crossmatchedQuantities.packedRBC.BMinus" />
                                <label for="crossmatchedBMinusPacked">B-</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="crossmatchedOPlusPacked" type="number" v-model="crossmatchedQuantities.packedRBC.OPlus" />
                                <label for="crossmatchedOPlusPacked">O+</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="crossmatchedOMinusPacked" type="number" v-model="crossmatchedQuantities.packedRBC.OMinus" />
                                <label for="crossmatchedOMinusPacked">O-</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="crossmatchedABPlusPacked" type="number" v-model="crossmatchedQuantities.packedRBC.ABPlus" />
                                <label for="crossmatchedABPlusPacked">AB+</label>
                            </FloatLabel>
                        </div>
                        <div class="mt-3">
                            <FloatLabel>
                                <InputText id="crossmatchedABMinusPacked" type="number" v-model="crossmatchedQuantities.packedRBC.ABMinus" />
                                <label for="crossmatchedABMinusPacked">AB-</label>
                            </FloatLabel>
                        </div>
                    </div>
                </div>
            </Fluid>
        </div>
    </div>
    <span class="font-bold mb-6 block">Requisition</span>
    <div class="">
        <div class="card p-4">
            <div v-for="(item, index) in requisitionItems" :key="index">
                <div class="mb-6 col-span-3 flex justify-between">
                    <span class="">Request #{{ index + 1 }}</span>
                    <div class="flex justify-end">
                        <Button @click="removeRequisitionItem(index)" icon="pi pi-times" severity="danger" rounded outlined></Button>
                    </div>
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
                            <Dropdown :options="bloodTypes" v-model="item.bloodType" optionLabel="label" optionValue="value" class="w-full" />
                            <label for="">Blood Type</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-1">
                        <FloatLabel>
                            <InputText type="number" v-model="item.quantity" class="w-full" />
                            <label for="">Quantity</label>
                        </FloatLabel>
                    </div>
                    <div class="col-span-2 mt-3">
                        <FloatLabel>
                            <InputText type="text" v-model="item.remarks" class="w-full" />
                            <label for="">Remarks <span class="font text-sm">(Optional)</span></label>
                        </FloatLabel>
                    </div>
                </div>
                <hr class="mt-4 mb-5" />
            </div>
            <div class="flex justify-center mb-4">
                <Button @click="addRequisitionItem" class="w-full p-button-secondary">Add <i class="pi pi-plus text-white-500"></i></Button>
            </div>
        </div>
    </div>
    <div class="flex justify-center mt-6">
        <Button @click="submitForm" class="p-button w-full">Submit Request</Button>
    </div>
</template>
