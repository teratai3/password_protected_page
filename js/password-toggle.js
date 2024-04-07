(function (Drupal, once) {
    Drupal.behaviors.passwordToggle = {
        attach: function (context, settings) {     
            // パスワード切り替えボタンに対する処理
            once('passwordToggle', '.password-toggle-button', context).forEach(function (element) {

                let passwordField = context.querySelector('#edit-field-page-password-0-value');
                if (passwordField) {
                    passwordField.type = 'password';
                }
                
                element.addEventListener('click', function (e) {
                    e.preventDefault();

                    // パスワードフィールドの取得
                    let passwordField = context.querySelector('#edit-field-page-password-0-value');
                    if (!passwordField) { return; }

                    // inputタイプの切り替え
                    passwordField.type = passwordField.type === 'password' ? 'text' : 'password';
                });
            });
        }
    };
})(Drupal, once);
