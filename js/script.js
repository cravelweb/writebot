jQuery(document).ready(function ($) {
  var jsonUrl = "/wp-content/plugins/ghostwriter/json/ghost.json";
  var jsonData = {};

  fetch(jsonUrl)
    .then((response) => response.json())
    .then((data) => {
      jsonData = data;

      function updateDescription() {
        var selectedType = $("select[name='types']").val();
        var description =
          jsonData["types"]["items"][selectedType]["description"];
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
          allPrompt +=
            jsonData[selectName]["items"][selectedGhostWriter]["prompt"] + " ";
        });

        var margedPrompt =
          "@制約条件:" +
          allPrompt +
          $('textarea[name="user_prompt"]').val() +
          " @テーマ:" +
          $('textarea[name="post_theme"]').val() +
          " @キーワード:" +
          $('textarea[name="post_keywords"]').val() +
          " @出力内容:";
        console.log(margedPrompt);

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
            $("#openai-api-status").html("生成中...");
            $("#ghost-writer-settings .spinner").addClass("is-active");
          },

          success: function (response) {
            if (response.success) {
              console.log(response.data);
              $('textarea[name="generated_content"]').val(response.data);
              $("#openai-api-status").html("文書生成が完了しました");
            } else {
              console.log(response.data);
              $("#openai-api-status").html("文書生成に失敗しました");
            }
          },

          error: function (response) {
            console.log(response);
            $("#openai-api-status").html("エラーが発生しました");
          },

          complete: function () {
            /*setTimeout(function() {
          $('#openai-api-status').html('');
        }, 3000);*/
            $("#generate_content").prop("disabled", false);
            $("#ghost-writer-settings .spinner").removeClass("is-active");
          },
        });
      });
    });
});
