@extends('shop::base')

@section('aimeos_header')
    <title>{{ __( 'Checkout') }}</title>
    <?= $aiheader['locale/select'] ?? '' ?>
    <?= $aiheader['checkout/standard'] ?>
    <?= $aiheader['catalog/search'] ?? '' ?>
    <?= $aiheader['catalog/tree'] ?? '' ?>
@stop

@section('aimeos_head')
    <?= $aibody['locale/select'] ?? '' ?>
@stop

@section('aimeos_nav')
    <?= $aibody['catalog/tree'] ?? '' ?>
    <?= $aibody['catalog/search'] ?? '' ?>
@stop

@section('aimeos_body')
    <div class="container">
        <?= $aibody['checkout/standard'] ?>
    </div>
@stop
