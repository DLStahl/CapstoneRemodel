<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DBEditorController extends Controller
{
    public static $customView = [
        'variables' => [
            'hide' => [],
            'uneditable' => ['name', 'description'],
        ],
        'evaluation_forms' => [
            'hide' => [],
            'uneditable' => ['form_type'],
        ],
    ];

    // WARNING: no security, but it's admin only and david said that's OK

    public function viewPage($table)
    {
        // get all columns
        $columns = DB::connection()
            ->getSchemaBuilder()
            ->getColumnListing($table);

        // remove 'id', 'created_at', and 'updated_at' from the columns, user should not worry about these
        $invisibleColumns = ['id', 'created_at', 'updated_at'];
        // list of column names that should be left uneditable by user - currently only disabled client side, API doesn't reject them being updated
        $uneditable = [];

        if (array_key_exists($table, self::$customView)) {
            $this_tables_config = self::$customView[$table];
            $hidden = $this_tables_config['hide'];
            $disallow_edits = $this_tables_config['uneditable'];
            foreach ($hidden as $column_to_hide) {
                array_push($invisibleColumns, $column_to_hide);
            }
            foreach ($disallow_edits as $column_to_disallow_edits) {
                array_push($uneditable, $column_to_disallow_edits);
            }
        }

        foreach ($invisibleColumns as $invisibleColumn) {
            $indexInColumns = array_search($invisibleColumn, $columns);
            if ($indexInColumns !== false) {
                unset($columns[$indexInColumns]);
            }
        }

        $data = collect(DB::table($table)->get())
            ->map(function ($x) {
                return (array) $x;
            })
            ->toArray();

        $primaryKeyField = DB::connection()
            ->getDoctrineSchemaManager()
            ->listTableIndexes($table)
            ['primary']->getColumns()[0];

        // technically exposing file structure with this, but it's semi-necessary and not dangerous if we sanitize
        $fullyQualifiedName = $table;

        return view(
            'crud.databasetable',
            compact('columns', 'data', 'primaryKeyField', 'fullyQualifiedName', 'uneditable')
        );
    }

    public function api_delete(Request $request)
    {
        try {
            DB::table($request->input('table'))
                ->where('id', $request->input('rowID'))
                ->delete();

            return json_encode([
                'successful' => 'true',
                'rowID' => $request->input('rowID'),
            ]);
        } catch (Exception $e) {
            return json_encode(['successful' => 'false']);
        }
    }

    public function api_update(Request $request)
    {
        try {
            DB::table($request->input('table'))
                ->where('id', $request->input('rowID'))
                ->update(json_decode($request->post()['data'], true));

            $newData = DB::table($request->input('table'))
                ->where('id', $request->input('rowID'))
                ->get()[0];

            return json_encode([
                'successful' => 'true',
                'newData' => $newData,
            ]);
        } catch (Exception $e) {
            return json_encode(['successful' => 'false']);
        }
    }

    public function api_add(Request $request)
    {
        try {
            $id = DB::table($request->input('table'))->insertGetId(json_decode($request->post()['data'], true));

            $newData = DB::table($request->input('table'))
                ->where('id', $id)
                ->get()[0];

            return json_encode([
                'successful' => 'true',
                'newData' => $newData,
            ]);
        } catch (Exception $e) {
            return json_encode(['successful' => 'false']);
        }
    }
}
