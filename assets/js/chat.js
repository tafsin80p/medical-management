jQuery(document).ready(function ($) {
    const $messageButton = $("#messageButton");
    const $chatbox = $("#chatbox");
    const $closeChatbox = $("#closeChatbox");
    const $chatboxMessages = $("#chatboxMessages");
    const $chatboxInput = $("#chatboxInput");
    const $sendChatMessage = $("#sendChatMessage");
    const $chatboxUserListButton = $("#chatboxUserListButton");
    const $chatboxUserList = $("#chatboxUserList");
    const $chatboxFileButton = $("#chatboxFileButton");
    const $chatboxFile = $("#chatboxFile");

    let recipientId = null;
    let recipientName = null;

    $messageButton.on("click", function () {
        $chatbox.toggleClass("hidden");
        if (!$chatbox.hasClass("hidden")) {
            if (chat_object.isAdmin) {
                fetchUsers();
            } else {
                recipientId = 1;
                fetchMessages();
            }
        }
    });

    $(document).on("click", ".start-chat", function () {
        recipientId = $(this).data("user-id");
        recipientName = $(this).data("user-name");

        $chatbox.removeClass("hidden");
        $chatboxUserListButton.text(recipientName);
        fetchMessages();
    });

    $closeChatbox.on("click", function () {
        $chatbox.addClass("hidden");
    });

    $sendChatMessage.on("click", function () {
        sendMessage();
    });

    $chatboxInput.on("keypress", function (e) {
        if (e.which === 13) {
            sendMessage();
        }
    });

    $chatboxFileButton.on("click", function () {
        $chatboxFile.click();
    });

    $chatboxUserListButton.on("click", function () {
        $chatboxUserList.toggleClass("hidden");
    });

    function fetchUsers() {
        $.get(chat_object.ajax_url, {
            action: "get_all_chat_users",
            nonce: chat_object.nonce,
        })
            .done(function (response) {
                if (response.success) {
                    displayUsers(response.data);
                }
            })
            .fail(function () {
                // Handle error
            });
    }

    function displayUsers(users) {
        $chatboxUserList.empty();
        users.forEach(function (user) {
            const onlineStatus = user.is_online ? "<span class=\"text-green-500\"> (Online)</span>" : "";
            const userHtml = `
                <div class="p-2 hover:bg-gray-100 cursor-pointer" data-user-id="${user.ID}">
                    ${user.display_name} ${onlineStatus}
                </div>
            `;
            $chatboxUserList.append(userHtml);
        });

        $chatboxUserList.find("div").on("click", function () {
            recipientId = $(this).data("user-id");
            $chatboxUserListButton.text($(this).text());
            $chatboxUserList.addClass("hidden");
            fetchMessages();
        });
    }

    function sendMessage() {
        if (!recipientId) {
            alert("Please select a user to chat with.");
            return;
        }

        const message = $chatboxInput.val().trim();
        const file = $chatboxFile[0].files[0];

        if (message === "" && !file) {
            return;
        }

        const formData = new FormData();
        formData.append("action", "send_chat_message");
        formData.append("nonce", chat_object.nonce);
        formData.append("recipient_id", recipientId);
        formData.append("message", message);
        formData.append("file", file);

        $.ajax({
            url: chat_object.ajax_url,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function () {
                $chatboxInput.val("");
                $chatboxFile.val("");
                fetchMessages();
            },
            error: function () {
                alert("Failed to send message.");
            },
        });
    }

    function fetchMessages() {
        if (!recipientId) {
            return;
        }

        $.get(chat_object.ajax_url, {
            action: "get_chat_messages",
            nonce: chat_object.nonce,
            other_user_id: recipientId,
        })
            .done(function (response) {
                if (response.success) {
                    displayMessages(response.data);
                }
            })
            .fail(function () {
                // Handle error
            });
    }

    function displayMessages(messages) {
        $chatboxMessages.empty();
        messages.forEach(function (message) {
            const isSender = parseInt(message.sender_id) !== recipientId;
            const messageClass = isSender ? "justify-end" : "justify-start";
            const bubbleClass = isSender ? "bg-blue-500 text-white" : "bg-gray-200 text-gray-800";

            let messageContent = "";
            if (message.message) {
                messageContent += `<p>${message.message}</p>`;
            }
            if (message.file_path) {
                messageContent += `<a href="${message.file_path}" target="_blank" class="text-blue-200 hover:underline">View File</a>`;
            }

            const messageHtml = `
                <div class="flex ${messageClass} mb-2">
                    <div class="${bubbleClass} rounded-lg px-4 py-2 max-w-xs">
                        ${messageContent}
                        <div class="text-xs mt-1 ${isSender ? 'text-blue-200' : 'text-gray-500'}">${message.created_at}</div>
                    </div>
                </div>
            `;
            $chatboxMessages.append(messageHtml);
        });
        $chatboxMessages.scrollTop($chatboxMessages[0].scrollHeight);
    }

    function updateUserStatus() {
        $.post(chat_object.ajax_url, {
            action: "update_user_status",
            nonce: chat_object.nonce,
        });
    }

    setInterval(fetchMessages, 500);
    setInterval(updateUserStatus, 10000);
});
