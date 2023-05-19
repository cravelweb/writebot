jQuery(document).ready(function ($) {
  var textdomain = "cravel-writebot";
  var jsonUrl = CravelChatGptAutopostAjax.ghostUrl;
  var jsonData = {};

  fetch(jsonUrl)
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then((data) => {
      jsonData = data;

      function updateDescription() {
        var selectedType = $("select[name='types']").val();
        var description =
          jsonData?.["types"]?.["items"]?.[selectedType]?.["description"] || "";
        $("#theme-description").text(description);
      }
      updateDescription();

      $("select[name='types']").change(updateDescription);

      $("#generate_content").click(function (e) {
        e.preventDefault();
        $(this).prop("disabled", true);

        var allPrompt = "";
        $("select.ghost").each(function () {
          var selectedGhostWriter = $(this).val();
          var selectName = $(this).attr("name");
          var prompt =
            jsonData?.[selectName]?.["items"]?.[selectedGhostWriter]?.[
              "prompt"
            ];
          allPrompt += prompt ? prompt + " " : "";
        });

        var selectedLang = $("select.selected_language").val();
        var lang = $("select.selected_language option:selected").text();

        var userPrompt = $('textarea[name="user_prompt"]').val();
        var postTheme = $('textarea[name="post_theme"]').val();
        var postKeywords = $('textarea[name="post_keywords"]').val();

        if (userPrompt.length > 2000) {
          alert("User prompt too long, it will be trimmed!");
          userPrompt = userPrompt.substring(0, 2000);
        }

        if (postTheme.length > 2000) {
          alert("Post theme too long, it will be trimmed!");
          postTheme = postTheme.substring(0, 2000);
        }

        if (postKeywords.length > 1000) {
          alert("Post keywords too long, they will be trimmed!");
          postKeywords = postKeywords.substring(0, 1000);
        }

        var margedPrompt = "";
        margedPrompt += allPrompt
          ? "@" + text_label.constraints + ":" + allPrompt
          : "";
        margedPrompt += userPrompt ? " - " + userPrompt : "";
        margedPrompt += lang ? " - " + lang : "";
        margedPrompt += postTheme
          ? " @" + text_label.theme + ":" + postTheme
          : "";
        margedPrompt += postKeywords
          ? " @" + text_label.keywords + ":" + postKeywords
          : "";
        margedPrompt += " @" + text_label.output + ":";
        //console.log(margedPrompt);

        $.ajax({
          type: "POST",
          url: CravelChatGptAutopostAjax.ajaxurl,
          data: {
            action: "generate_content",
            nonce: CravelChatGptAutopostAjax.nonce,
            post_id: $("#post_ID").val(),
            prompt: margedPrompt,
          },

          beforeSend: function () {
            $("#openai-api-status").html(text_label.generating);
            $("#writebot-metabox .spinner").addClass("is-active");
          },

          success: function (response) {
            if (response.success) {
              //console.log(response.data);
              $('textarea[name="generated_content"]').val(response.data);
              $("#openai-api-status").html(text_label.generated);
            } else {
              //console.log(response.data);
              $("#openai-api-status").html(text_label.error);
            }
          },

          error: function (response) {
            //console.log(response);
            $("#openai-api-status").html(text_label.error);
          },

          complete: function () {
            $("#generate_content").prop("disabled", false);
            $("#writebot-metabox .spinner").removeClass("is-active");
          },
        });
      });
    })

    .catch((error) => {
      console.error(
        "There has been a problem with your fetch operation:",
        error
      );
    });
});
