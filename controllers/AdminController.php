<?php
class AdminController {
 private function offset(): int { return (max(1,min(10000,(int)($_GET['page'] ?? 1)))-1)*20; }
 public function index(): void { require_user(true);redirect('admin_messages'); }
 public function users(): void { require_user(true);render_view('admin_users',['title'=>'Usuários','rows'=>(new User())->page($this->offset())]); }
 public function messages(): void { require_user(true);render_view('admin_messages',['title'=>'Mensagens','rows'=>(new Message())->page($this->offset())]); }
 public function audit(): void { require_user(true);render_view('admin_audit_log',['title'=>'Auditoria','rows'=>(new Audit())->page($this->offset())]); }
 public function edit(): void { require_user(true);$target=(new User())->find((int)($_GET['id']??0));if(!$target){(new PaginaController())->index();return;}render_view('edit_user',['title'=>'Permissão de acesso','target'=>$target]); }
 public function permission(): void { post_guard();$actor=require_user(true);$role=input('user_type');if(!in_array($role,['adm','usu'],true)){http_response_code(422);render_view('error',['title'=>'Permissão inválida','message'=>'Escolha um nível de acesso válido.']);return;}$ok=(new User())->changeRole((int)$actor['id'],(int)input('id'),$role);flash($ok?'Permissão atualizada.':'Não é possível remover o último administrador.',$ok?'success':'error');redirect('admin_users'); }
 public function status(): void {post_guard();$actor=require_user(true);$status=input('status');if(!in_array($status,Message::STATUSES,true)){http_response_code(422);render_view('error',['title'=>'Situação inválida','message'=>'Escolha uma situação válida.']);return;}(new Message())->status((int)input('id'),$status,(int)$actor['id']);flash('Situação da mensagem atualizada.');redirect('admin_messages');}
}
