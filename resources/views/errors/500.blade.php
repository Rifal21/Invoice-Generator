@extends('errors::minimal')

@section('title', __('Masalah Server'))
@section('code', '500')
@section('code-icon', 'X')
@section('message', __('Kesalahan Sistem'))
@section('description', __('Terjadi kesalahan internal pada server kami. Tim teknis sedang berusaha memperbaikinya.'))
