<?php

namespace App\Controller;
use Cake\Collection\Collection;
use PHPUnit\TextUI\XmlConfiguration\LogToReportMigration;

class ArticlesController extends AppController
{

    public function initalize(): void
    {
        parent::initialize();

        $this->loadComponent('Paginator');
        $this->loadComponent('Flash'); //Include the FlashComponent
    }

    public function index()
    {
        $this->loadComponent('Paginator');
        $articles = $this->Paginator->paginate($this->Articles->find());
        $this->set(compact('articles'));
    }

    public function view($slug = null)
    {
        $article = $this->Articles
                ->findBySlug($slug)
                ->contain('Tags')
                ->firstOrFail();

        $this->set(compact('article'));
    }

    public function add()
    {
        $article = $this->Articles->newEmptyEntity();
        if ($this->request->is('post')) {
            $article = $this->Articles->patchEntity($article, $this->request->getData());

            //Hardcoding the user_id is temporary, and will be removed later
            // when we build authentication out.
            $article->user_id = 1;

            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been saved'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to add your article'));
        }

        //Get a list of tags
        $tags = $this->Articles->Tags->find('list')->all();

        // Set tags to the view context
        $this->set('tags', $tags);

        $this->set('article', $article);
    }

    public function edit($slug)
    {
        $article = $this->Articles
            ->findBySlug($slug)
            ->contain('Tags')
            ->firstOrFail();

        if ($this->request->is(['post', 'put'])) {
            $this->Articles->patchEntity($article, $this->request->getData());
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been updated.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to update your article.'));
        }

        // Get all tags list
        $tags = $this->Articles->Tags->find('list')->all();
        // Set tags to the view context
        $this->set('tags', $tags);
        $this->set('article', $article);
    }

    public function delete($slug)
    {
        $this->request->allowMethod(['post', 'delete']);

        $article = $this->Articles->findBySlug($slug)->firstOrFail();

        if($this->Articles->delete($article)){
            $this->Flash->success(__('The {0} article has been deleted. ', $article->title ));
            return $this->redirect(['action' => 'index']);
        }
    }

    public function tags()
    {

        $tags = $this->request->getParam('pass');
        // Find those articles which is used passes parameters
        $articles = $this->Articles->find('tagged', [
            'tags' => $tags
        ])
        ->all();

        // Pass variables into the view
        $this->set([
            'articles' => $articles,
            'tags' => $tags
        ]);
    }

    public function test(){

    }

}
