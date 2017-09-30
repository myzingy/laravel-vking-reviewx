<?php

namespace App\Admin\Controllers;

use App\Review;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Input;

class ReviewController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Review::class, function (Grid $grid) {
            global $conf;
            $conf=config('review');
            if($appid=Input::get('appid')){
                $grid->model()->where('appid','=',$appid);
            }
            $grid->id('ID')->sortable();
            $grid->model()->with(['cont','attr']);
            // 第二列显示title字段，由于title字段名和Grid对象的title方法冲突，所以用Grid的column()方法代替
            $grid->column('type','Type')->display(function ($type) {

                return $type==Review::TYPE_REVIEW?'评论':'问题';

            });
            $grid->column('product','Product')->display(function () {
                return '<a href="'.$this->cont['page_url'].'" target="_blank">'.$this->target_id . '/' .
                    $this->target_sku.'</a>';
            });
            $grid->column('brand','Brand')->display(function () {
                global $conf;
                return $conf[$this->appid]['brand'];
            });
            $grid->created_at();
            //$grid->status('Status');
            // 设置text、color、和存储值
            $states = [
                '已通过'  => ['value' => 0, 'text' => '已通过', 'color' => 'primary'],
                '审核中' => ['value' => 1, 'text' => '审核中', 'color' => 'default'],
            ];
            $grid->column('review','Review')->display(function () {
                return
                    '<div style="max-width: 300px; word-break: break-all">Nickname:'.$this->cont['nickname'].'<br/>'
                    .($this->cont['email']?('Email:'.$this->cont['email'].'<br/>'):"")
                    .($this->type==Review::TYPE_REVIEW?('Summary:'.htmlentities($this->cont['summary']).'<br/>'):"")
                    .($this->is_attr==Review::IS_ATTR_HAVING?'<span class="label label-primary">图</span>':'')
                    .htmlentities($this->cont['review'])
                    .($this->type==Review::TYPE_REVIEW?""
                        :("<br>".($this->cont['reply']?"<span class=\"label label-success\">已回复</span>"
                                :"<span class=\"label label-danger\">未回复</span>"))
                    ).'</div>';
            });
            $grid->column('score','Score')->display(function () {
                return $this->type==Review::TYPE_REVIEW?($this->score.'星'):"/";
            });
            $grid->status('Status')->select(['已通过','待审核','垃圾评论'], $states);
            $grid->model()->orderBy('created_at','desc');

            // filter($callback)方法用来设置表格的简单搜索框
            $grid->filter(function($filter){
                // 如果过滤器太多，可以使用弹出模态框来显示过滤器.
                $filter->useModal();
                // 禁用id查询框
                $filter->disableIdFilter();
                $filter->like('target_sku', 'SKU');

                $filter->is('type', '评论/问题')->select([0=>'只看评论',1=>'只看问题']);
                $filter->is('status', '状态')->select([0=>'已通过',1=>'待审核',2=>'垃圾评论']);
                // 关系查询，查询对应关系`profile`的字段
                $filter->where(function ($query) {

                    $input = $this->input;

                    $query->whereHas('cont', function ($query) use ($input) {
                        $query->where('nickname', 'like', "%{$input}%");
                    });

                }, 'Nickname');
                $filter->where(function ($query) {

                    $input = $this->input;

                    $query->whereHas('cont', function ($query) use ($input) {
                        $query->where('email', 'like', "%{$input}%");
                    });

                }, 'Email');
                $filter->where(function ($query) {

                    $input = $this->input;

                    $query->whereHas('cont', function ($query) use ($input) {
                        $query->where('page_title', 'like', "%{$input}%");
                    });

                }, 'Page Title');
                $filter->where(function ($query) {

                    $input = $this->input;

                    $query->whereHas('cont', function ($query) use ($input) {
                        $query->where('page_url', 'like', "%{$input}%");
                    });

                }, 'Page URL');
            });
            
            //disableExport
            $grid->disableExport();
            //disableCreation
            $grid->disableCreation();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Review::class, function (Form $form) {
            $form->model()->with(['cont','attr']);
            $form->display('id', 'ID');
            $form->select('status','Status')->options(['已通过','审核中','垃圾评论']);
            $form->display('type','Type')->with(function ($type) {
                return $type==Review::TYPE_REVIEW?'评论':'问题';
            });
            $form->display('product','Product')->with(function () {
                return '<a href="'.$this->cont['page_url'].'" target="_blank">('.$this->target_id . '/' .
                    $this->target_sku.') '.$this->cont['page_url'].'</a>';
            });
            $form->display('review','Review')->with(function () {
                return 'Nickname:'.$this->cont['nickname'].'<br/>'
                    .($this->cont['email']?('Email:'.$this->cont['email'].'<br/>'):"")
                    .($this->type==Review::TYPE_REVIEW?('Summary:'.$this->cont['summary'].'<br/>'):"")
                    .$this->cont['review'];
            });
            $form->display('Score')->with(function () {
                if($this->type!=Review::TYPE_REVIEW) return "";
                return '<span style="color:yellowgreen;">'
                    .str_replace('1',
                        '<span class="glyphicon glyphicon-star"></span>',
                        str_pad('',$this->score,'1')
                    )
                    .'</span>';
            });
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
            $form->display('Images')->with(function () {
                if($this->is_attr!=Review::IS_ATTR_HAVING) return "";
                $attr='<div>';
                foreach ($this->attr as $r){
                    $attr.='<a href="/review/image/'.($r['attr_id']).'-x-full.png" target="_blank">'.
                        '<img src="/review/image/'.($r['attr_id']).'-x-100.png" alt="..." class="img-rounded"></a>';
                }
                return $attr.'</div>';
            });
            $form->textarea('cont.reply','Reply')->rows(10);

            $form->hidden('type');
            $form->saved(function (Form $form) {
                if($form->type==Review::TYPE_QUESTION && $form->cont['reply']){
                    $form->model()->status=Review::STATUS_SUCCESS;
                    $form->model()->save();
                }
            });
        });
    }
}
