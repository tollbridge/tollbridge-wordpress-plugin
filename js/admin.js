const PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_USERS_WITH_CONFIGURED_PLANS = 2;

const listen = document.querySelectorAll("[name='tollbridge_time_access_change']");
if (listen.length) {
    // Any global settings need hiding?
    const globalDependent = document.querySelectorAll('.tollbridge_global_option');
    globalDependent.forEach(function (item) {
        item.parentNode.parentNode.classList.add('tollbridge_global_option');
        if (item.classList.contains('hidden')) {
            item.parentNode.parentNode.classList.add('hidden');
        }
    });

    // Any paywall eligibility settings need hiding?
    const globalDependentEligibility = document.querySelectorAll(".tollbridge_eligibility_check_behavior_dependent");
    globalDependentEligibility.forEach(function (item) {
        item.parentNode.parentNode.classList.add("tollbridge_eligibility_check_behavior_dependent");
        if (item.classList.contains("hidden")) {
            item.parentNode.parentNode.classList.add("hidden");
        }
    });

    const listenEligibility = document.querySelectorAll(
        ".tollbridge_paywall_eligibility_check_behaviour"
    );

    if (listenEligibility.length) {
        listenEligibility.forEach(function (node) {
            node.addEventListener("click", function (event) {
                togglePaywallEligibilityCheckBehaviorDependencies(this.value != PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_USERS_WITH_CONFIGURED_PLANS);
                toggleArticlePaywallEligibilityCheckBehaviorDependencies(getPaywallEligibilityCheckBehaviorValue() != PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_USERS_WITH_CONFIGURED_PLANS)
            });
        });
    }

    if (getPaywallEligibilityCheckBehaviorValue() != PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_USERS_WITH_CONFIGURED_PLANS) {
        togglePaywallEligibilityCheckBehaviorDependencies(true);
        toggleArticlePaywallEligibilityCheckBehaviorDependencies(true)
    }

    function toggleGlobalDependencies(hide) {
        const dependents = document.querySelectorAll('.tollbridge_global_option');
        dependents.forEach(function (item) {
            if (hide) {
                item.classList.add('hidden');
            } else {
                item.classList.remove('hidden');
            }

            if (!hide) {
                togglePaywallEligibilityCheckBehaviorDependencies(getPaywallEligibilityCheckBehaviorValue() != PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_USERS_WITH_CONFIGURED_PLANS);
                toggleArticlePaywallEligibilityCheckBehaviorDependencies(getPaywallEligibilityCheckBehaviorValue() != PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_USERS_WITH_CONFIGURED_PLANS)
            }
        });
    }

    function togglePaywallEligibilityCheckBehaviorDependencies(hide) {
        const items = document.querySelectorAll(".tollbridge_eligibility_check_behavior_dependent");
        items.forEach(function (node) {
            if (hide) {
                node.classList.add("hidden");
            } else {
                node.classList.remove("hidden");
            }
        });
    }

    function toggleArticlePaywallEligibilityCheckBehaviorDependencies(hide) {
        const items = document.querySelectorAll(".tollbridge_article_eligibility_check_behavior_dependent");
        items.forEach(function (node) {
            if (hide) {
                node.classList.add("hidden");
            } else {
                node.classList.remove("hidden");
            }
        });
    }

    function getPaywallEligibilityCheckBehaviorValue() {
        if (
            document.querySelector(
                '[name="tollbridge_paywall_eligibility_check_behaviour"]:checked'
            )
        ) {
            return document.querySelector(
                '[name="tollbridge_paywall_eligibility_check_behaviour"]:checked'
            ).value;
        }

        return false;
    }

    function toggleGlobalPostDependencies(hide) {
        const dependents = document.querySelectorAll('.tollbridge-override-settings');
        dependents.forEach(function (item) {
            if (hide) {
                item.classList.add('hidden');
            } else {
                item.classList.remove('hidden');
            }
        });
    }

    function toggleTimeVisibility(hide) {
        const items = document.querySelectorAll('.tollbridge_time_access_dependent');
        items.forEach(function (node) {
            if (hide) {
                node.classList.add('hidden');
            } else {
                node.classList.remove('hidden');
            }
        });
    }

    function togglePaywallTextVisibility(hide) {
        const items = document.querySelectorAll('.tollbridge_change_message_paywall');
        items.forEach(function (node) {
            if (hide) {
                node.classList.add('hidden');
            } else {
                node.classList.remove('hidden');
            }
        });
    }

    const toggleRadio = function (toggleInput, toggleMethod) {
        if (document.querySelector('[name="' + toggleInput + '"]:checked')) {
            toggleMethod(document.querySelector('[name="' + toggleInput + '"]:checked').value == 0);
        }
        document.querySelectorAll('[name="' + toggleInput + '"]').forEach(function (item) {
            item.addEventListener('click', function (event) {
                toggleMethod(this.value == 0);
            });
        });
    };

    toggleRadio('tollbridge_is_using_global_rules', toggleGlobalDependencies);
    toggleRadio('tollbridge_override_global_rules', toggleGlobalPostDependencies);
    toggleRadio('tollbridge_time_access_change', toggleTimeVisibility);
    toggleRadio('tollbridge_change_message_paywall', togglePaywallTextVisibility);
}

const selectAllCheckbox = (selector) => {
    let checkboxes = document.querySelectorAll(selector);

    for (let checkbox of checkboxes) {
        checkbox.checked = true;
    }
}

const unselectAllCheckbox = (selector) => {
    let checkboxes = document.querySelectorAll(selector);

    for (let checkbox of checkboxes) {
        checkbox.checked = false;
    }
}
