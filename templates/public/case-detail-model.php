<?php
defined('ABSPATH') || exit;

?>
<!-- Case Details Modal -->
<div id="caseModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex justify-center p-4">
        <div style="height:90vh" class="bg-white rounded-lg shadow-xl max-w-4xl w-full overflow-y-scroll">
            <!-- Modal Header -->
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Case Details</h3>
                <button id="closeCaseModal" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div id="modalContent" class="p-6">
                <div class="flex justify-center items-center h-32">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                </div>
            </div>
        </div>
    </div>
</div>