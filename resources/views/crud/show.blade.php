@extends('layouts.app')@section('title','Detail')@section('content')<pre class="card">{{ print_r($item->toArray(),true) }}</pre>@endsection
