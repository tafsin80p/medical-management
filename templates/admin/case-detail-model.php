<?php
defined('ABSPATH') || exit;
?>
<!-- Case Details Modal -->
<div id="caseDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex justify-center p-4 mt-10">
        <div style="height:90vh" class="bg-white rounded-lg shadow-xl max-w-4xl w-full overflow-y-scroll">
            <!-- Modal Header -->
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Case Details</h3>
                <button id="closeCaseDetailsModal" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="-0.5 0 25 25"
                        fill="none">
                        <path d="M3 21.32L21 3.32001" stroke="#000000" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M3 3.32001L21 21.32" stroke="#000000" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div id="caseDetailsModalContent" class="p-6">
                <div class="flex justify-center items-center h-32">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                </div>
            </div>
        </div>
    </div>
</div>