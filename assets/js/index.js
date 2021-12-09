jQuery(document).ready(function ($) {
  const loginWithCredentialsButton = $("#login-with-credentials-button");
  const loginWithMobileButton = $("#login-with-mobile-button");

  const loginWithMobile = $("#login-with-mobile");
  const loginWithCredentials = $("#login-with-credentials");
  const verificationCodeStep = $("#verification-code-step");
  const verifyAndRegisterStep = $("#verify-and-register-step");

  const loginWithMobileForm = $("#login-with-mobile form");
  const loginWithCredentialsForm = $("#login-with-credentials form");
  const verificationCodeStepForm = $("#verification-code-step form");
  const verifyAndRegisterStepForm = $("#verify-and-register-step form");

  loginWithCredentialsButton.on("click", (event) => {
    event.preventDefault();
    loginWithMobile.hide();
    loginWithCredentials.show();
  });

  loginWithMobileButton.on("click", (event) => {
    event.preventDefault();
    loginWithCredentials.hide();
    loginWithMobile.show();
  });

  loginWithCredentialsForm.on("submit", (event) => {
    event.preventDefault();
    const submitBtn = $(event.target).find("[type=submit]");
    submitBtn.prop("disabled", true);
    const formData = new FormData(event.target);
    const formObject = Object.fromEntries(formData);

    ajax($, {
      type: "POST",
      dataType: "json",
      url: "/wp-json/advance-login/v1/login-with-credentials",
      data: {
        username: formObject.username,
        password: formObject.password,
      },
      success: function (res) {
        switch (res.type) {
          case "login":
            window.location.href = res.redirect_url;
            break;
          default:
            throw new Error("invalid response type");
        }
      },
      error: function () {
        submitBtn.prop("disabled", false);
      },
    });
  });

  let loginWithMobileResponse = null;
  loginWithMobileForm.on("submit", (event) => {
    event.preventDefault();
    loginWithMobileResponse = null;
    const submitBtn = $(event.target).find("[type=submit]");
    submitBtn.prop("disabled", true);
    const formData = new FormData(event.target);
    const formObject = Object.fromEntries(formData);

    ajax($, {
      type: "POST",
      dataType: "json",
      url: "/wp-json/advance-login/v1/request-login",
      data: {
        mobile: formObject.mobile,
      },
      success: function (res) {
        loginWithMobile.hide();
        verificationCodeStep.show();
        loginWithMobileResponse = res;
      },
      error: function () {
        loginWithMobile.show();
        verificationCodeStep.hide();
      },
      complete: function () {
        submitBtn.prop("disabled", false);
      },
    });
  });

  let verificationCodeStepResponse = null;
  verificationCodeStepForm.on("submit", (event) => {
    event.preventDefault();
    verificationCodeStepResponse = null;
    const submitBtn = $(event.target).find("[type=submit]");
    submitBtn.prop("disabled", true);
    const formData = new FormData(event.target);
    const formObject = Object.fromEntries(formData);
    ajax($, {
      type: "POST",
      dataType: "json",
      url: "/wp-json/advance-login/v1/verify-login",
      data: {
        code: formObject.code,
        mobile: loginWithMobileResponse.mobile,
      },
      success: function (res) {
        verificationCodeStepResponse = res;
        switch (res.type) {
          case "register":
            verificationCodeStep.hide();
            verifyAndRegisterStep.show();
            break;
          case "login":
            window.location.href = res.redirect_url;
            break;
          default:
            throw new Error("invalid response type");
        }
      },
      error: function () {
        submitBtn.prop("disabled", false);
      },
    });
  });

  verifyAndRegisterStepForm.on("submit", (event) => {
    event.preventDefault();
    const submitBtn = $(event.target).find("[type=submit]");
    submitBtn.prop("disabled", true);
    const formData = new FormData(event.target);
    const formObject = Object.fromEntries(formData);
    ajax($, {
      type: "POST",
      dataType: "json",
      url: "/wp-json/advance-login/v1/verify-and-register",
      data: {
        code: verificationCodeStepResponse.code,
        mobile: loginWithMobileResponse.mobile,
        first_name: formObject.first_name,
        last_name: formObject.last_name,
        password: formObject.password,
      },
      success: function (res) {
        switch (res.type) {
          case "login":
            window.location.href = res.redirect_url;
            break;
          default:
            throw new Error("invalid response type");
        }
      },
      error: function () {
        submitBtn.prop("disabled", false);
      },
    });
  });
});

function ajax($, config) {
  return $.ajax({
    ...config,
    error: function (error) {
      if (typeof config.error === "function") {
        config.error(error);
      }
      console.log("error", error);
      alert(error.responseJSON.message);
    },
  });
}
