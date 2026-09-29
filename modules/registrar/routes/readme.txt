     
  i use fastRoute for routing so make sure that the variable of the object are 
   ($r) for accessibility of the other methods. 

  For documentation see: https://github.com/nikic/FastRoute?tab=readme-ov-file



  # how to attach middleware 

  [
  'middleware' => ['auth'],
  'uses' => [Controller::class, 'method']
 ]

  #sample :

  $r->addGroup('/posts', function ($r) {

    $r->addRoute('GET', '', [
        'uses' => [PostController::class, 'index'],
        'as'   => 'posts.index',
    ]);

    $r->addRoute('GET', '/create', [
        'uses' => [PostController::class, 'create'],
        'as'   => 'posts.create',
    ]);

    $r->addRoute('POST', '', [
        'uses' => [PostController::class, 'store'],
        'as'   => 'posts.store',
    ]);

    $r->addRoute('GET', '/{id:\d+}/edit', [
        'uses' => [PostController::class, 'edit'],
        'as'   => 'posts.edit',
    ]);

    $r->addRoute('POST', '/{id:\d+}', [
        'uses' => [PostController::class, 'update'],
        'as'   => 'posts.update',
    ]);

    $r->addRoute('POST', '/{id:\d+}/delete', [
        'uses' => [PostController::class, 'destroy'],
        'as'   => 'posts.destroy',
    ]);

});



  # please feel free to delete me.