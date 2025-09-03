<?php
defined('ABSPATH') or die('No direct access.');
?>

<!-- Multi-Step Form Container -->
<div id="case-form-container"
    class="fixed top-0 left-0 w-full h-screen bg-gray-500 bg-opacity-80 z-50 overflow-auto flex justify-center items-start pt-10 hidden">
    <div id="intake-form-container"
        class="w-11/12 md:w-10/12 lg:w-8/12 xl:w-6/12 2xl:w-4/12 bg-white rounded-lg shadow-lg p-4">

        <!-- Header -->
        <div class="bg-blue-600 rounded-t-lg p-4 sm:p-6 text-white">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                <div>
                    <h1 class="text-2xl font-bold mb-1">New Client Intake Form</h1>
                    <p class="text-sm sm:text-base text-white/60">Secure HIPAA-Compliant Submission</p>
                </div>
                <div class="text-3xl sm:text-4xl">🛡️</div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="flex items-center justify-between mb-6 mt-4">
            <div class="flex-1 flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-white font-bold step-circle active"
                    data-step="1">1</div>
                <div class="flex-1 h-1 bg-gray-300 step-line"></div>
            </div>
            <div class="flex-1 flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-white font-bold step-circle"
                    data-step="2">2</div>
                <div class="flex-1 h-1 bg-gray-300 step-line"></div>
            </div>
            <div class="flex-1 flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-white font-bold step-circle"
                    data-step="3">3</div>
                <div class="flex-1 h-1 bg-gray-300 step-line"></div>
            </div>
            <div class="flex-1 flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-white font-bold step-circle"
                    data-step="4">4</div>
                <div class="flex-1 h-1 bg-gray-300 step-line"></div>
            </div>
            <div class="flex-1 flex items-center">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-white font-bold step-circle"
                    data-step="5">5</div>
            </div>
        </div>

        <!-- Form -->
        <form id="intake-form" method="post" enctype="multipart/form-data" class="space-y-6">

            <!-- Step 1: Personal Information -->
            <div class="step" id="step-1">
                <h2 class="font-bold text-lg mb-2">Personal Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input type="text" name="first_name" placeholder="First Name *" required
                        class="border px-3 py-2 rounded-md w-full">
                    <input type="text" name="last_name" placeholder="Last Name *" required
                        class="border px-3 py-2 rounded-md w-full">
                    <input type="email" name="email" placeholder="Email *" required
                        class="border px-3 py-2 rounded-md w-full">
                    <input type="tel" name="phone" placeholder="Phone *" required
                        class="border px-3 py-2 rounded-md w-full">
                    <input type="date" name="dob" placeholder="Date of Birth *" required
                        class="border px-3 py-2 rounded-md w-full">
                    <input type="text" name="va_file_number" placeholder="VA File Number *" required
                        class="border px-3 py-2 rounded-md w-full">
                    <input type="text" name="address" placeholder="Address *" required
                        class="border px-3 py-2 rounded-md w-full md:col-span-3">
                    <input type="text" name="city" placeholder="City *" required
                        class="border px-3 py-2 rounded-md w-full">
                    <select name="state" required class="border px-3 py-2 rounded-md w-full">
                        <option value="">Select State</option>
                        <option value="AL">Alabama</option>
                        <option value="AK">Alaska</option>
                        <!-- Add all states -->
                    </select>
                    <input type="text" name="zip_code" placeholder="ZIP *" required
                        class="border px-3 py-2 rounded-md w-full">
                </div>
            </div>

            <!-- Step 2: Service History (Single Period) -->
            <div class="step hidden" id="step-2">
                <h2 class="font-bold text-lg mb-2">Service History</h2>
                <div id="service-history-container">
                    <div class="service-entry border p-4 rounded-md">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <select name="service_branch[]" required class="border px-3 py-2 rounded-md">
                                <option value="">Branch of Service *</option>
                                <option value="army">Army</option>
                                <option value="air_force">Air Force</option>
                                <option value="navy">Navy</option>
                                <option value="marine_corps">Marine Corps</option>
                                <option value="coast_guard">Coast Guard</option>
                                <option value="space_force">Space Force</option>
                            </select>
                            <select name="service_composition[]" required class="border px-3 py-2 rounded-md">
                                <option value="">Service Composition *</option>
                                <option value="active_duty">Active Duty</option>
                                <option value="reserve">Reserve</option>
                                <option value="national_guard">National Guard</option>
                            </select>
                            <input type="text" name="mos_aoc_rate[]" placeholder="MOS/AOC/AOS/Rate"
                                class="border px-3 py-2 rounded-md">
                            <input type="text" name="duty_position[]" placeholder="Duty Position"
                                class="border px-3 py-2 rounded-md">
                        </div>

                        <!-- Deployments -->
                        <div class="deployments-container mt-8">
                            <h4 class="font-semibold mb-4">Deployments</h4>

                            <!-- Deployment entry template -->
                            <div class="deployment-entry grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                                <input type="text" name="deployment_location[0][]" placeholder="Location"
                                    class="border px-3 py-2 rounded-md">
                                <input type="text" name="deployment_dates[0][]" placeholder="Dates in Theater"
                                    class="border px-3 py-2 rounded-md">
                                <input type="text" name="deployment_job[0][]" placeholder="Job/Mission Set"
                                    class="border px-3 py-2 rounded-md">
                            </div>

                            <!-- Add Deployment Button -->
                            <button type="button"
                                class="add-deployment mt-8 mx-auto block text-blue-600 cursor-pointer">
                                Add Deployment
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: VA Claim Information -->
            <div class="step hidden" id="step-3">
                <h2 class="font-bold text-lg mb-2">VA Claim Information</h2>
                <div id="va-claims-container" class="space-y-4">
                    <div class="va-claim-entry border p-4 rounded-md">
                        <input type="text" name="condition[]" placeholder="Condition *" required
                            class="border px-3 py-2 rounded-md w-full mb-2">
                        <select name="condition_type[]" required class="border px-3 py-2 rounded-md w-full mb-2">
                            <option value="">Primary/Secondary *</option>
                            <option value="primary">Primary</option>
                            <option value="secondary">Secondary</option>
                        </select>
                        <textarea name="primary_event[]" placeholder="If Primary: Event Details"
                            class="border px-3 py-2 rounded-md w-full mb-2"></textarea>
                        <input type="text" name="secondary_linked[]" placeholder="If Secondary: Linked Condition"
                            class="border px-3 py-2 rounded-md w-full mb-2">
                        <textarea name="service_explanation[]" placeholder="Why caused/aggravated by service?"
                            class="border px-3 py-2 rounded-md w-full mb-2"></textarea>
                        <select name="mtf_seen[]" class="border px-3 py-2 rounded-md w-full mb-2">
                            <option value="0">Seen at Military Treatment Facility? No</option>
                            <option value="1">Yes</option>
                        </select>
                        <textarea name="mtf_details[]" placeholder="MTF Details"
                            class="border px-3 py-2 rounded-md w-full mb-2"></textarea>
                        <textarea name="current_treatment[]" placeholder="Current Treatment"
                            class="border px-3 py-2 rounded-md w-full mb-2"></textarea>
                    </div>
                </div>
                <button type="button" id="add-va-claim" class="mt-2 block text-blue-600 mt-8 mx-auto">Add Another
                    Condition
                </button>
            </div>


            <!-- Step 4: Document Upload -->
            <div class="step hidden" id="step-4">
                <h2 class="text-xl font-semibold text-center mb-8">📄 Document Upload</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Upload Card -->
                    <div
                        class="flex flex-col items-center py-4 bg-white border-2 border-dashed rounded-2xl shadow-sm hover:shadow-md transition">
                        <label for="dd214" class="cursor-pointer flex flex-col items-center space-y-2">
                            <span class="text-4xl">📄</span>
                            <span class="text-sm font-medium text-gray-700">Upload DD214</span>
                            <span class="text-xs text-gray-400">(Required)</span>
                        </label>
                        <input id="dd214" type="file" name="documents_dd214[]" required class="hidden">
                        <ul id="dd214-preview" class="mt-3 space-y-1 text-sm text-gray-600"></ul>
                    </div>

                    <!-- Medical Records -->
                    <div
                        class="flex flex-col items-center py-4 bg-white border-2 border-dashed rounded-2xl shadow-sm hover:shadow-md transition">
                        <label for="medical" class="cursor-pointer flex flex-col items-center space-y-2">
                            <span class="text-4xl">📑</span>
                            <span class="text-sm font-medium text-gray-700">Upload Medical Records</span>
                            <span class="text-xs text-gray-400">(Multiple, Required)</span>
                        </label>
                        <input id="medical" type="file" name="documents_medical[]" required multiple class="hidden">
                        <ul id="medical-preview" class="mt-3 space-y-1 text-sm text-gray-600"></ul>
                    </div>

                    <!-- VA Rating Decision -->
                    <div
                        class="flex flex-col items-center py-4 bg-white border-2 border-dashed rounded-2xl shadow-sm hover:shadow-md transition">
                        <label for="rating" class="cursor-pointer flex flex-col items-center space-y-2">
                            <span class="text-4xl">📝</span>
                            <span class="text-sm font-medium text-gray-700">Upload Rating Decision</span>
                            <span class="text-xs text-gray-400">(Required)</span>
                        </label>
                        <input id="rating" type="file" name="documents_rating[]" required class="hidden">
                        <ul id="rating-preview" class="mt-3 space-y-1 text-sm text-gray-600"></ul>
                    </div>

                    <!-- VA Decision Letters -->
                    <div
                        class="flex flex-col items-center py-4 bg-white border-2 border-dashed rounded-2xl shadow-sm hover:shadow-md transition">
                        <label for="decision" class="cursor-pointer flex flex-col items-center space-y-2">
                            <span class="text-4xl">📬</span>
                            <span class="text-sm font-medium text-gray-700">Upload Decision Letters</span>
                            <span class="text-xs text-gray-400">(Multiple, Required)</span>
                        </label>
                        <input id="decision" type="file" name="documents_decision[]" required multiple class="hidden">
                        <ul id="decision-preview" class="mt-3 space-y-1 text-sm text-gray-600"></ul>
                    </div>

                    <!-- Optional -->
                    <div
                        class="flex flex-col items-center py-4 bg-white border-2 border-dashed rounded-2xl shadow-sm hover:shadow-md transition">
                        <label for="optional" class="cursor-pointer flex flex-col items-center space-y-2">
                            <span class="text-4xl">📂</span>
                            <span class="text-sm font-medium text-gray-700">Upload Optional Docs</span>
                            <span class="text-xs text-gray-400">(Optional, Multiple)</span>
                        </label>
                        <input id="optional" type="file" name="documents_optional[]" multiple class="hidden">
                        <ul id="optional-preview" class="mt-3 space-y-1 text-sm text-gray-600"></ul>
                    </div>

                </div>
            </div>



            <!-- Step 5: Consent -->
            <div class="step hidden" id="step-5">
                <h2 class="font-bold text-lg mb-2">Consent & Agreement</h2>
                <div class="space-y-2">
                    <label class="flex items-center gap-2"><input type="checkbox" name="data_consent" required> I
                        consent to PHI/PII collection.</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="privacy_consent" required> I
                        agree to Privacy Policy & Terms.</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="communication_consent" required>
                        I consent to communication.</label>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="flex justify-between mt-6">
                <button type="button" id="prev-btn" class="px-4 py-2 bg-gray-300 rounded-md">Back</button>
                <button type="button" id="next-btn" class="px-4 py-2 bg-blue-600 text-white rounded-md">Next</button>
                <button type="submit" id="submit-btn"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hidden">Submit</button>
            </div>

        </form>
    </div>
</div>

<script>

</script>