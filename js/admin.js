const PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_USERS_WITH_CONFIGURED_PLANS = 2;

var listen = document.querySelectorAll(
  "[name='tollbridge_time_access_change']"
);

if (listen.length) {
  listen.forEach(function (node) {
    node.addEventListener("click", function (event) {
      toggleTimeVisibility(this.value == 0);
    });
  });

  // Any global settings need hiding?
  var globalDependent = document.querySelectorAll(".tollbridge_global_option");
  globalDependent.forEach(function (item) {
    item.parentNode.parentNode.classList.add("tollbridge_global_option");
    if (item.classList.contains("hidden")) {
      item.parentNode.parentNode.classList.add("hidden");
    }
  });

   // Any paywall eligibilty settings need hiding?
   var globalDependent = document.querySelectorAll(".tollbridge_eligibilty_check_behavior_dependent");
   globalDependent.forEach(function (item) {
     item.parentNode.parentNode.classList.add("tollbridge_eligibilty_check_behavior_dependent");
     if (item.classList.contains("hidden")) {
       item.parentNode.parentNode.classList.add("hidden");
     }
   });

  if (
    document.querySelector('[name="tollbridge_is_using_global_rules"]:checked')
  ) {
    if (
      document.querySelector(
        '[name="tollbridge_is_using_global_rules"]:checked'
      ).value != "1"
    ) {
      toggleGlobalDependencies(true);
    }
  }

  var listen = document.querySelectorAll(
    ".tollbridge_paywall_eligibility_check_behaviour"
  );

  if (listen.length) {
    listen.forEach(function (node) {
      node.addEventListener("click", function (event) {
        togglePaywallEligibiltyCheckBehaviorDependencies(this.value != PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_USERS_WITH_CONFIGURED_PLANS);
        toggleArticlePaywallEligibiltyCheckBehaviorDependencies(getPaywallEligibiltyCheckBehaviorValue() != PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_USERS_WITH_CONFIGURED_PLANS)
      });
    });
  }

  if (
    getPaywallEligibiltyCheckBehaviorValue() != PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_USERS_WITH_CONFIGURED_PLANS
  ) {
    togglePaywallEligibiltyCheckBehaviorDependencies(true);
    toggleArticlePaywallEligibiltyCheckBehaviorDependencies(true)
  }

  document
    .querySelectorAll(".tollbridge_global_radio")
    .forEach(function (item) {
      item.addEventListener("click", function (event) {
        toggleGlobalDependencies(
          this.value != 1
        );

        if(this.value == 1) {
            togglePaywallEligibiltyCheckBehaviorDependencies(getPaywallEligibiltyCheckBehaviorValue() != PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_USERS_WITH_CONFIGURED_PLANS);
            toggleArticlePaywallEligibiltyCheckBehaviorDependencies(getPaywallEligibiltyCheckBehaviorValue() != PAYWALL_ELIGIBILITY_BEHAVIOR_OPEN_TO_USERS_WITH_CONFIGURED_PLANS)
        }
       
      });
    });

  function toggleGlobalDependencies(hide) {
    var dependents = document.querySelectorAll(".tollbridge_global_option");
    dependents.forEach(function (item) {
      if (hide) {
        item.classList.add("hidden");
      } else {
        item.classList.remove("hidden");
      }
    });
  }

  function toggleTimeVisibility(hide) {
    var items = document.querySelectorAll(".tollbridge_time_access_dependent");
    items.forEach(function (node) {
      if (hide) {
        node.classList.add("hidden");
      } else {
        node.classList.remove("hidden");
      }
    });
  }

  function togglePaywallEligibiltyCheckBehaviorDependencies(hide) {
    var items = document.querySelectorAll(
      ".tollbridge_eligibilty_check_behavior_dependent"
    );
    items.forEach(function (node) {
      if (hide) {
        node.classList.add("hidden");
      } else {
        node.classList.remove("hidden");
      }
    });
  }

  function toggleArticlePaywallEligibiltyCheckBehaviorDependencies(hide) {
    var items = document.querySelectorAll(
      ".tollbridge_article_eligibilty_check_behavior_dependent"
    );
    items.forEach(function (node) {
      if (hide) {
        node.classList.add("hidden");
      } else {
        node.classList.remove("hidden");
      }
    });
  }

  function getPaywallEligibiltyCheckBehaviorValue() {
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
}

document
  .querySelectorAll('[name="tollbridge_override_global_rules"]')
  .forEach(function (item) {
    item.addEventListener("click", function (event) {
      document
        .querySelector(".tollbridge-override-settings")
        .classList.toggle("hidden");
    });
  });

const selectAllCheckbox = (selector) => {
  let checkboxes = document.querySelectorAll(selector);

  for (let checkbox of checkboxes) {
    checkbox.checked = true;
  }
};

const unselectAllCheckbox = (selector) => {
  let checkboxes = document.querySelectorAll(selector);

  for (let checkbox of checkboxes) {
    checkbox.checked = false;
  }
};
