<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class DocumentController extends Controller
{
    private function owned(Request $r, int $id)
    {
        return DB::table('documents')->where('user_id', $r->user()->id)->where('id', $id)->first() ?? abort(404);
    }

    public function index(Request $r)
    {
        return response()->json(DB::table('documents')->where('user_id', $r->user()->id)->orderByDesc('updated_at')->get()->map(fn ($d) => $this->decode($d)))->header('Cache-Control', 'no-store');
    }

    private function decode($d)
    {
        $d->content = json_decode($d->content, true);

        return $d;
    }

    public function save(Request $r, ?int $id = null)
    {
        $d = $r->validate(['kind' => 'required|in:cv,letter,portfolio', 'title' => 'required|string|max:150', 'content' => 'required|array', 'content.name' => 'nullable|string|max:150', 'content.role' => 'nullable|string|max:200', 'content.email' => 'nullable|string|max:254', 'content.phone' => 'nullable|string|max:100', 'content.city' => 'nullable|string|max:200', 'content.link' => 'nullable|string|max:500', 'content.summary' => 'nullable|string|max:10000', 'content.experience' => 'nullable|string|max:30000', 'content.education' => 'nullable|string|max:10000', 'content.skills' => 'nullable|string|max:10000', 'content.projects' => 'nullable|string|max:15000', 'content.languages' => 'nullable|string|max:3000', 'content.certifications' => 'nullable|string|max:10000', 'content.language' => 'nullable|in:tr,en', 'content.photo' => ['nullable', 'string', 'max:100000', 'regex:/^data:image\/(jpeg|png|webp);base64,[A-Za-z0-9+\/=]+$/'], 'content.portfolioTemplate' => 'nullable|in:spark,block,stage,together,line,flow,identity,stroke,frame,plan,wave,workshop,layers,trajectory,bulletin,signature,gallery,shot', 'content.template' => 'nullable|string|max:40', 'content.color' => 'nullable|regex:/^#[0-9a-fA-F]{6}$/', 'content.font' => 'nullable|in:sans,serif,mono', 'content.text' => 'nullable|string|max:50000', 'content.company' => 'nullable|string|max:200']);
        if (strlen(json_encode($d['content'])) > 150000) {
            abort(422, 'İçerik çok büyük.');
        }$original = $id ? $this->owned($r, $id) : null;
        if ($original && $original->kind !== $d['kind']) {
            abort(422, 'Belge türü değiştirilemez.');
        }$id = DB::transaction(function () use ($r, $d, $id, $original) {
            if ($original) {
                DB::table('document_versions')->insert(['document_id' => $id, 'title' => $original->title, 'content' => $original->content, 'created_at' => now(), 'updated_at' => now()]);
                DB::table('documents')->where('id', $id)->update(['title' => $d['title'], 'content' => json_encode($d['content']), 'updated_at' => now()]);

                return $id;
            }

            return DB::table('documents')->insertGetId(['user_id' => $r->user()->id, 'kind' => $d['kind'], 'title' => $d['title'], 'content' => json_encode($d['content']), 'created_at' => now(), 'updated_at' => now()]);
        });

        return response()->json($this->decode($this->owned($r, $id)));
    }

    public function versions(Request $r, int $id)
    {
        $this->owned($r, $id);

        return response()->json(DB::table('document_versions')->where('document_id', $id)->orderByDesc('id')->get()->map(fn ($d) => $this->decode($d)));
    }

    public function restore(Request $r, int $id, int $version)
    {
        $original = $this->owned($r, $id);
        $v = DB::table('document_versions')->where('document_id', $id)->where('id', $version)->first() ?? abort(404);
        DB::transaction(function () use ($original, $v, $id) {
            DB::table('document_versions')->insert(['document_id' => $id, 'title' => $original->title, 'content' => $original->content, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('documents')->where('id', $id)->update(['title' => $v->title, 'content' => $v->content, 'updated_at' => now()]);
        });

        return response()->json($this->decode($this->owned($r, $id)));
    }

    public function destroy(Request $r, int $id)
    {
        $this->owned($r, $id);
        DB::table('documents')->where('id', $id)->delete();

        return response()->json(['message' => 'Belge silindi.']);
    }

    public function share(Request $r, int $id)
    {
        $doc = $this->owned($r, $id);
        abort_unless($doc->kind === 'portfolio', 422);
        $d = $r->validate(['published' => 'required|boolean']);
        $slug = $d['published'] ? ($doc->share_slug ?? Str::random(32)) : null;
        DB::table('documents')->where('id', $id)->update(['share_slug' => $slug]);

        return response()->json(['url' => $slug ? url('/p/'.$slug) : null]);
    }

    public function portfolio(string $slug)
    {
        $doc = DB::table('documents')->where('kind', 'portfolio')->where('share_slug', $slug)->first() ?? abort(404);

        return response()->view('portfolio', ['data' => json_decode($doc->content, true), 'title' => $doc->title])->header('X-Robots-Tag', 'noindex');
    }
}
