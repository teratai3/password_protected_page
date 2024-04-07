<?php

namespace Drupal\password_protected_page\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\password_protected_page\PasswordClass;
use Symfony\Component\HttpFoundation\Cookie;
use Drupal\Core\Url;
use Drupal\Core\Cache\Cache;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PasswordForm extends FormBase
{
  public function getFormId()
  {
    return 'password_form';
  }
  
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $form['password'] = [
      '#type' => 'password',
      '#title' => $this->t('Password'),
      '#required' => true,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }


  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $node_id = !empty($form_state->getBuildInfo()['args'][0]) ? $form_state->getBuildInfo()['args'][0] : null;
    $PasswordClass = new PasswordClass($node_id);

    if ($PasswordClass->validatePass($form_state->getValue('password'))) {

      Cache::invalidateTags(['node:' . $node_id]); //hook_node_viewのキャッシュをクリア

      $cookie = new Cookie(
        $PasswordClass->getCookieName(),
        password_hash($form_state->getValue('password'), PASSWORD_DEFAULT),
        time() + (3600 * 24 * 1), // 1日の有効期限
        '/',
        $_SERVER["HTTP_HOST"], // ドメイン
        false, // セキュア https
        true // HttpOnly（JavaScriptからのアクセスを禁止）
      );

      $url = Url::fromRoute('entity.node.canonical', ['node' => $node_id])->toString();
      $response = new RedirectResponse($url);
      $response->headers->setCookie($cookie);
      $form_state->setResponse($response);

      //$form_state->setRedirect('entity.node.canonical', ['node' => $node_id]);
    } else {
      $this->messenger()->addError("パスワードが違います。");
    }
  }
}
