<?php
lmb_require('limb/web_app/src/controller/lmbController.class.php');
lmb_require('limb/mail/src/lmbMailer.class.php');
lmb_require('limb/cms/src/model/lmbCmsUser.class.php');

class UserController extends lmbController
{
  function doForgotPassword()
  {
    if($this->request->hasPost())
    {
      $user = lmbActiveRecord :: findFirst('lmbCmsUser',
                                      array('email = ?', $this->request->get('email')));
      if(!$user)
      {
        $this->flashError("Sorry, we can't find a user with email you specified", array('Field' => 'email'));
        return;
      }

      $this->useForm('password_form');

      if($this->error_list->isEmpty())
      {
        $password = $user->generatePassword();
        $user->setNewPassword($password);
        $user->setGeneratedPassword($user->cryptPassword($password));
        $user->saveSkipValidation();

        $template = new lmbWactView('user/forgot_password_email.txt');
        $template->setCacheDir(LIMB_VAR_DIR . '/compiled/');
        $template->set('user', $user);
        $template->set('approve_password_url',
                       'http://'.$_SERVER['HTTP_HOST'] . '/user/approve/'.$user->getGeneratedPassword());
        $email_body = $template->render();
        $mailer = new lmbMailer();
        $mailer->sendPlainMail($user->getEmail(),
                               ADMIN_EMAIL,
                               "Password recovery",
                               $email_body);
        $this->response->redirect('/');
        $this->flashMessage('New password was generated and send to your email');
      }
    }
  }

  function doApprove()
  {
    $user = lmbActiveRecord :: findFirst('lmbCmsUser', array('generated_password = ?',
                                                             $this->request->get('id')));
    if(!$user)
      $this->error_list->addError("Sorry, we can't find a user with email you specified", array('Field' => 'email'));

    $this->useForm('password_form{$event_date}');

    if($this->error_list->isEmpty())
    {
      $user->setHashedPassword($user->getGeneratedPassword());
      $user->setGeneratedPassword('');
      $user->saveSkipValidation();
      $this->response->redirect('/');
      $this->flashMessage('New password activated!');
    }
  }

  function doLogin()
  {
    if($this->request->hasPost())
    {
      $user = $this->toolkit->getCmsUser();

      $login = $this->request->get('login');
      $password = $this->request->get('password');

      if(!$redirect_url = urldecode($this->request->get('redirect')))
        $redirect_url = '/';

      if($user->login($login, $password))
        $this->toolkit->redirect($redirect_url);
      else
        $this->flashError("Wrond login or password");

    }
  }

  function doLogout()
  {
    $user = $this->toolkit->getCmsUser();
    $user->logout();
    $this->response->redirect('/');
  }
}


