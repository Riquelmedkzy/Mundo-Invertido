<?php
class ContactController {
 public function index(): void { render_view('contact',['title'=>'Faça contato']); }
 public function send(): void {
  post_guard();throttle('contact',5);$name=input('name');$email=input('email');$subject=input('subject');$message=input('message');
  if(strlen($name)<2 || strlen($name)>100 || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($email)>150 || strlen($subject)>150 || strlen($message)<10 || strlen($message)>5000) {
   http_response_code(422);render_view('contact',['title'=>'Faça contato','errors'=>['Revise os campos. A mensagem deve ter entre 10 e 5.000 caracteres.'],'old'=>compact('name','email','subject','message')]);return;
  }
  $u=current_user();(new Message())->create($u?(int)$u['id']:null,$name,$email,$subject,$message);flash('Mensagem recebida. Obrigado por fazer contato.');redirect('contact');
 }
}
