@extends('layouts.iframe')
@if($data['view']=="" || $data['view']=="list")
@section('content-list')
    <el-row :gutter="20">
        <el-col>
            <oneday-comment-list :param='{!! $data_json !!}' ref="oneday-comment-list"></oneday-comment-list>
        </el-col>
    </el-row>
@endsection
@endif

@if($data['view']=="" || $data['view']=="form")
@section('content-form')
    <el-row :gutter="20">
        <el-col>
            <oneday-comment-form :param='{!! $data_json !!}' ref="oneday-comment-form"></oneday-comment-form>
        </el-col>
    </el-row>
@endsection
@endif

@if($data['view']=="me-list")
@section('content-list')
    <el-row :gutter="20">
        <el-col>
            <oneday-me-list :param='{!! $data_json !!}' ref="oneday-me-list"></oneday-me-list>
        </el-col>
    </el-row>
@endsection
@endif