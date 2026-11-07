<?php
class PaginaController {
 public function index(): void { http_response_code(404);render_view('404',['title'=>'Caminho desconhecido']); }
 public function home(): void { render_view('home',['title'=>'Mundo Invertido','pageClass'=>'home-page']); }
 public function portfolio(): void { render_view('portfolio',['title'=>'Temporadas de Stranger Things','pageClass'=>'explore-page']); }
}
