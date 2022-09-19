var listen = document.querySelectorAll('[name=\'tollbridge_time_access_change\']');
if (listen.length) {
    listen.forEach(function (node) {
        node.addEventListener('click', function (event) {
            toggleTimeVisibility((this.value == 0));
        });
    });

    // Any global settings need hiding?
    var globalDependent = document.querySelectorAll('.tollbridge_global_option');
    globalDependent.forEach(function (item) {
        item.parentNode.parentNode.classList.add('tollbridge_global_option');
        if (item.classList.contains('hidden')) {
            item.parentNode.parentNode.classList.add('hidden');
        }
    });

    if (document.querySelector('[name="tollbridge_is_using_global_rules"]:checked')) {
        if (document.querySelector('[name="tollbridge_is_using_global_rules"]:checked').value != '1') {
            toggleGlobalDependencies(true);
        }
    }
    document.querySelectorAll('.tollbridge_global_radio').forEach(function (item) {
        item.addEventListener('click', function (event) {
            toggleGlobalDependencies(this.value != 1);
        });
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
}


document.querySelectorAll('[name="tollbridge_override_global_rules"]').forEach(function (item) {
    item.addEventListener('click', function (event) {
        document.querySelector('.tollbridge-override-settings').classList.toggle('hidden');
    });
});

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
