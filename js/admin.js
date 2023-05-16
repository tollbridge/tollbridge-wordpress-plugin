var listen = document.querySelectorAll("[name='tollbridge_time_access_change']");
if (listen.length) {
    // Any global settings need hiding?
    var globalDependent = document.querySelectorAll('.tollbridge_global_option');
    globalDependent.forEach(function (item) {
        item.parentNode.parentNode.classList.add('tollbridge_global_option');
        if (item.classList.contains('hidden')) {
            item.parentNode.parentNode.classList.add('hidden');
        }
    });

    function toggleGlobalDependencies(hide) {
        var dependents = document.querySelectorAll('.tollbridge_global_option');
        dependents.forEach(function (item) {
            if (hide) {
                item.classList.add('hidden');
            } else {
                item.classList.remove('hidden');
            }
        });
    }

    function toggleGlobalPostDependencies(hide) {
        var dependents = document.querySelectorAll('.tollbridge-override-settings');
        dependents.forEach(function (item) {
            if (hide) {
                item.classList.add('hidden');
            } else {
                item.classList.remove('hidden');
            }
        });
    }

    function toggleTimeVisibility(hide) {
        var items = document.querySelectorAll('.tollbridge_time_access_dependent');
        items.forEach(function (node) {
            if (hide) {
                node.classList.add('hidden');
            } else {
                node.classList.remove('hidden');
            }
        });
    }

    function togglePaywallTextVisibility(hide) {
        var items = document.querySelectorAll('.tollbridge_change_message_paywall');
        items.forEach(function (node) {
            if (hide) {
                node.classList.add('hidden');
            } else {
                node.classList.remove('hidden');
            }
        });
    }

    var toggleRadio = function (toggleInput, toggleMethod) {
        if (document.querySelector('[name="' + toggleInput + '"]:checked')) {
            toggleMethod(document.querySelector('[name="' + toggleInput + '"]:checked').value == 0);
        }
        document.querySelectorAll('[name="' + toggleInput + '"]').forEach(function (item) {
            item.addEventListener('click', function (event) {
                toggleMethod(this.value == 0);
            });
        });
    }

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
