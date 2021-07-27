<div [hidden]="hideLeaky" amp-access="<?= implode(' AND ', array_map(function ($plan) {
    return 'plan != ' . $plan;
}, array_column($plans, 'id'))) ?> AND views >= 0 AND type = 'leaky'">
    <template amp-access-template type="amp-mustache">
        <div class="fixed bottom-0 left-0 w-full items-end justify-center px-4 py-6 pointer-events-none sm:p-6 sm:items-start sm:justify-end z-100000">
            <div class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
                <div class="p-4 text-center">
                    <div class="flex items-start">
                        <div class="ml-3 w-0 flex-1 pt-0.5"><p class="text-lg font-medium text-gray-900">
                                {{ widget.popupTitle }} </p>
                            <p class="mt-1 text-base text-gray-500"> {{ widget.popupBody }} </p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button on="tap:AMP.setState({ hideLeaky: true })"
                                    class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <span class="sr-only">Close</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                     aria-hidden="true" class="h-5 w-5">
                                    <path fill-rule="evenodd"
                                          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                          clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center border-l border-gray-200 pb-4">
                    <a on="tap:amp-access.login"
                       class="inline-flex items-center px-2.5 py-1.5 text-base font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                       style="color: {{ widget.button_background }};"> {{ widget.button }} </a>
                </div>
            </div>
        </div>
    </template>
</div>
