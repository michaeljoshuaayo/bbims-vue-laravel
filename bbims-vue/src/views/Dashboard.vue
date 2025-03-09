<template>
    <div class="grid grid-cols-12 gap-8">
        <div class="col-span-12 lg:col-span-6 xl:col-span-3">
            <div class="card mb-0">
                <div class="flex justify-between mb-4">
                    <div>
                        <span class="block text-muted-color font-medium mb-4">Blood Units in Stock</span>
                        <div class="text-surface-900 dark:text-surface-0 font-medium text-xl">252</div>
                    </div>
                    <div class="flex items-center justify-center bg-green-100 dark:bg-green-400/10 rounded-border" style="width: 2.5rem; height: 2.5rem">
                        <i class="pi pi-plus text-green-500 !text-xl"></i>
                    </div>
                </div>
                <span class="text-primary font-medium">24 new </span>
                <span class="text-muted-color">since last 24 hours</span>
            </div>
        </div>
        <div class="col-span-12 lg:col-span-6 xl:col-span-3">
            <div class="card mb-0">
                <div class="flex justify-between mb-4">
                    <div>
                        <span class="block text-muted-color font-medium mb-4">Distributed Blood Units</span>
                        <div class="text-surface-900 dark:text-surface-0 font-medium text-xl">331</div>
                    </div>
                    <div class="flex items-center justify-center bg-orange-100 dark:bg-orange-400/10 rounded-border" style="width: 2.5rem; height: 2.5rem">
                        <i class="pi pi-truck text-orange-500 !text-xl"></i>
                    </div>
                </div>
                <span class="text-primary font-medium">124 </span>
                <span class="text-muted-color">distributed since last week</span>
            </div>
        </div>
        <div class="col-span-12 lg:col-span-6 xl:col-span-3">
            <div class="card mb-0">
                <div class="flex justify-between mb-4">
                    <div>
                        <span class="block text-muted-color font-medium mb-4">Blood Requests</span>
                        <div class="text-surface-900 dark:text-surface-0 font-medium text-xl">
                            26
                            <span class="text-muted-color">total requests</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center bg-cyan-100 dark:bg-cyan-400/10 rounded-border" style="width: 2.5rem; height: 2.5rem">
                        <i class="pi pi-envelope text-cyan-500 !text-xl"></i>
                    </div>
                </div>
                <span class="text-primary font-medium">2 </span>
                <span class="text-muted-color">pending requests</span>
            </div>
        </div>
        <div class="col-span-12 lg:col-span-6 xl:col-span-3">
            <div class="card mb-0">
                <div class="flex justify-between mb-4">
                    <div>
                        <span class="block text-muted-color font-medium mb-4">Discarded Blood Units</span>
                        <div class="text-surface-900 dark:text-surface-0 font-medium text-xl">152</div>
                    </div>
                    <div class="flex items-center justify-center bg-red-100 dark:bg-red-400/10 rounded-border" style="width: 2.5rem; height: 2.5rem">
                        <i class="pi pi-trash text-red-500 !text-xl"></i>
                    </div>
                </div>
                <span class="text-primary font-medium">24 </span>
                <span class="text-muted-color">discarded since last 24 hours</span>
            </div>
        </div>

        <!-- <div class="col-span-12 xl:col-span-6">
            
            <div class="card">
                <div class="font-semibold text-xl mb-4">Blood Arrivals</div>
                <span class="block text-muted-color font-medium mb-4">TODAY</span>
                <ul class="p-0 m-0 list-none">
                    <li class="flex items-center py-2 border-b border-surface">
                        <div class="w-12 h-12 flex items-center justify-center bg-blue-100 dark:bg-blue-400/10 rounded-full mr-4 shrink-0">
                            <i class="pi pi-truck !text-xl text-blue-500"></i>
                        </div>
                        <span class="text-surface-900 dark:text-surface-0 leading-normal">
                            <span class="text-primary font-bold">10</span> units of blood arrived from Sorsogon Provincial Hospital.
                        </span>
                    </li>
                    <li class="flex items-center py-2 border-b border-surface">
                        <div class="w-12 h-12 flex items-center justify-center bg-red-100 dark:bg-red-400/10 rounded-full mr-4 shrink-0">
                            <i class="pi pi-truck !text-xl text-red-500"></i>
                        </div>
                        <span class="text-surface-900 dark:text-surface-0 leading-normal">
                            <span class="text-primary font-bold">5</span> units of blood arrived from Daraga Doctors Hospital.
                        </span>
                    </li>
                </ul>
                <span class="block text-muted-color font-medium mb-4">LAST WEEK</span>
                <ul class="p-0 m-0 list-none">
                    <li class="flex items-center py-2 border-b border-surface">
                        <div class="w-12 h-12 flex items-center justify-center bg-green-100 dark:bg-green-400/10 rounded-full mr-4 shrink-0">
                            <i class="pi pi-truck !text-xl text-green-500"></i>
                        </div>
                        <span class="text-surface-900 dark:text-surface-0 leading-normal">
                            <span class="text-primary font-bold">20</span> units of blood arrived from Camalig Provincial Hospital.
                        </span>
                    </li>
                    <li class="flex items-center py-2 border-b border-surface">
                        <div class="w-12 h-12 flex items-center justify-center bg-purple-100 dark:bg-purple-400/10 rounded-full mr-4 shrink-0">
                            <i class="pi pi-truck !text-xl text-purple-500"></i>
                        </div>
                        <span class="text-surface-900 dark:text-surface-0 leading-normal">
                            <span class="text-primary font-bold">15</span> units of blood arrived from Sorsogon Provincial Hospital .
                        </span>
                    </li>
                </ul>
            </div>
        </div> -->
    </div>
</template>
