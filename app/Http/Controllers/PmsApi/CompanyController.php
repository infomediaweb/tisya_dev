<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\TblCompany;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\Paginator;
use App\helper\MasterHelper;
use App\Models\TblState;
use DB;

class CompanyController extends Controller
{
    
    
    public function index(Request $request)
    {
        
        try {
            $query = TblCompany::query();

            if ($request->status) {
                $query->where('status', $request->status);
            }
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where('company_name', 'like', $search . '%');
            }
            // Take the counting of per page data
            $perPage = $request->has('take') ? $request->get('take') : 20;

            // convert these data into pagination
            $data = $query->paginate($perPage);

            return response()->json([
                'status' => true,
                'data' => $data,               
                'message' => 'Successfully Retrive'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }

    }
    
    
    
    /**
     * Store a newly created resource in storage.
    */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'company_name' => 'required|unique:tbl_companies',
                'state_id' => 'required',
                'gst_no' => 'required|unique:tbl_companies',
                //'cin_no' => 'required|unique:tbl_companies',
                'cin_no' => 'required',
            ]);
    
            // Customize validation messages for gst_no and cin_no
            $validator->setCustomMessages([
                'gst_no.unique' => 'The GST no has already been taken.',
                //'cin_no.unique' => 'The CIN no has already been taken.',
                'company_name.unique' => 'The name has already been taken.',
            ]);
    
            if ($validator->fails()) {
                $errors = $validator->errors();
    
                $gstNoError = $errors->first('gst_no');
                //$cinNoError = $errors->first('cin_no');
                $companyError = $errors->first('company_name');
    
                if ($gstNoError) {
                    $errorMessage = 'GST no and CIN no have already been taken.';
                } elseif ($gstNoError) {
                    $errorMessage = $gstNoError;
                } elseif($companyError){
                    $errorMessage = $companyError;
                }
    
                return response()->json([
                    'status' => false,
                    'message' => $errorMessage,
                ], 422);
            }
    
            $state_id = $request->state_id;
            $stateData = TblState::find($state_id);
            $state_name = $stateData->name;
            $state_code = $stateData->state_code;
    
            $company = new TblCompany();
            $company->company_name = $request->company_name;
            $company->company_address = $request->company_address;
            $company->state_id = $request->state_id;
            $company->state_name = $state_name;
            $company->state_code = $state_code;
            $company->gst_no = $request->gst_no;
            $company->cin_no = $request->cin_no;
            $company->company_phone = $request->company_phone;
            $company->company_email = $request->company_email;
            $company->company_website = $request->company_website;
            $company->add_ip = $request->ip();
            $company->add_by = Auth::user()->name;
            
            $company->save();
            
            return response()->json([
                'status' => true,
                'message' => 'Company added successfully.'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error occurred while saving the company.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    

    public function show(Request $request, $id)
    {
        //
        try {
            $data = TblCompany::find($id);
            if ($data) {
                return response()->json([
                    'status' => true,
                    'data' => $data,
                    'message' => 'Successfully Retrive.'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Something Went Wrong'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error'
            ], 500);
        }
    }




    public function update(Request $request, string $id)
    {
        //
        try {
            
            $SQL = TblCompany::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'company_name' => 'required',
                'state_id' => 'required',
                'gst_no' => ['required',Rule::unique('tbl_companies')->ignore($id)],
                //'cin_no' => ['required',Rule::unique('tbl_companies')->ignore($id)],
                'cin_no' => 'required',
            ]);

            $validator->setCustomMessages([
                'gst_no.unique' => 'The GST no has already been taken.',
                //'cin_no.unique' => 'The CIN no has already been taken.',
            ]);
          

            if ($validator->fails()) {
                $errors = $validator->errors();
                $gstNoError = $errors->first('gst_no');
               // $cinNoError = $errors->first('cin_no');
    
                if ($gstNoError) {
                    $errorMessage = 'GST no and CIN no have already been taken.';
                } elseif ($gstNoError) {
                    $errorMessage = $gstNoError;
                } 
    
                return response()->json([
                    'status' => false,
                    'message' => $errorMessage,
                ], 422);
            }
          
            $checkDuplicate = TblCompany::where('state_id','=', $request->state_id)
                              ->where('company_name', '=', $request->company_name)
                              ->where('id','!=',$id)
                              ->get();
        
            if($checkDuplicate->count() > 0){
                $errors = [];
                return response()->json([
                    'status' => false,
                    'message' => 'The name has already been taken',
                    'errors' => $errors,
                ], 422);
            } 
            
            $state_id = $request->state_id;
            $stateData = TblState::find($state_id); // without array
            $state_name = $stateData->name;
            $state_code = $stateData->state_code;         
            

            $SQL->company_name = $request->company_name;
            $SQL->company_address = $request->company_address;
            $SQL->state_id = $request->state_id;
            $SQL->state_name = $state_name;
            $SQL->state_code = $state_code;
            $SQL->gst_no = $request->gst_no;
            $SQL->cin_no = $request->cin_no;
            $SQL->company_phone = $request->company_phone;
            $SQL->company_email = $request->company_email;
            $SQL->company_website = $request->company_website;         
            $SQL->update_ip           = $request->ip();
            $SQL->update_by           = Auth::user()->name;

            $SQL->save();

            return response()->json([
                'status' => true,
                'message' => 'Successfully Updated.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }




    public function updateStatus(Request $request, $id)
     {
         try {
             $data = TblCompany::findOrFail($id);
             $request->validate([
                 'status' => 'required|boolean',
             ]);
             $data->status = $request->status;
 
             if ($data->save()) {
                 return response()->json([
                     'status' => true,
                     'message' => 'Status updated successfully'
                 ], 201);
             } else {
                 return response()->json([
                     'status' => false,
                     'message' => 'Something Went Wrong'
                 ], 400);
             }
         } catch (\Exception $e) {
             return response()->json([
                 'status' => false,
                 'message' => 'Internal Error',
                 'error' => $e->getMessage()
             ], 500);
         }
     }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try {
            $data = TblCompany::findOrFail($id);
            if ($data) {
                $data->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Successfully Deleted.'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'No record found'
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteMultipleRecord(Request $request)
    {
        //

        try {
            $ids = $request->get('ids');
            
            if (!empty($ids)) {
                TblCompany::whereIn('id', $ids)->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Successfully deleted selected records.',
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid or empty IDs provided.',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
