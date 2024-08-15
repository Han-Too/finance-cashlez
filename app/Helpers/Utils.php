<?php

namespace App\Helpers;

use App\Models\Applicant;
use App\Models\Channel;
use App\Models\DokumenApplicant;
use App\Models\HistoryApproval;
use App\Models\MasterPrivilege;
use App\Models\Merchant;
use App\Models\MerchantDocument;
use App\Models\MerchantPayment;
use App\Models\Privilege;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Unique;
use Image;
use InvalidArgumentException;
use Psy\Readline\Hoa\FileException;

class Utils
{
    public static function uploadImageOri($image)
    {
        try {
            $imageName = time() . uniqid() . '.' . $image->extension();
            // if (env('APP_ENV') == 'production' || !env('APP_ENV')) {
            //     $image->move('images', $imageName);
            // } else {
            //     $image->move(public_path('images'), $imageName);
            // }

            //PRODUCTION
            // $destinationPath = public_path('uploads');
            // $temporaryPath = $image->getPathname();
            // $destinationFilePath = $destinationPath . '/' . $imageName;

            // try {
            //     if (!move_uploaded_file($temporaryPath, $destinationFilePath)) {
            //         throw new FileException('Failed to move file.');
            //     }
            // } catch (FileException $e) {
            //     return response()->json(['message' => 'Failed to upload file: ' . $e->getMessage()], 500);
            // }
            //END PRODUCTION


            $image->move('public/images', $imageName);
            $path = url('images/' . $imageName);
            Log::info($path);
            return $path;
        } catch (\Throwable $th) {
            Log::info($th);
            return false;
        }
    }

    public static function uploadFile($image, $uuid)
    {
        try {
            $imageName = $uuid . '.' . $image->extension();
            if (env('APP_ENV') == 'production' || !env('APP_ENV')) {
                $image->move('images', $imageName);
            } else {
                $image->move(public_path('images'), $imageName);
            }
            $path = url('images/' . $imageName);
            return $path;
        } catch (\Throwable $th) {
            Log::info($th);
            return false;
        }
    }

    public static function generateToken()
    {
        return csrf_token();
    }

    public static function countTotalRoles($roleId)
    {
        return User::where('role', $roleId)->where('status', 'active')->count();
    }

    public static function restOfPrivilege($roleId)
    {
        $privilege = Privilege::where('role_id', $roleId)->where('status', 'active')->count();
        return 5 - $privilege;
    }

    public static function calculateMerchantPayment($bankTransfer, $feeMdrMerchant, $feeBankMerchant, $taxPayment)
    {
        $calculate = $bankTransfer - (($feeMdrMerchant - $feeBankMerchant) + $taxPayment);
        return $calculate;
    }

    public static function calculateTreshold($trxCount)
    {
        return (2 + 1) * $trxCount;
    }
    
    public static function getStatusReconcile($treshold, $boSettlement, $bankSettlement)
    {
        if (($bankSettlement - $boSettlement) < $treshold &&
            ($bankSettlement - $boSettlement) > (0 - $treshold) 
        ) {
            return "MATCH";
            // if(($bankSettlement - $boSettlement) == 100 || (($bankSettlement - $boSettlement) / $sales * 100) == 1){
            // } else {
            //     return "NOT_MATCH";
            // }
        } else {
            return "NOT_MATCH";
        }
    }

    public static function getNewStatusReconcile($sales, $diff)
    {
        if ($diff == 100 && ($diff / $sales * 100) == 1) {
            return "MATCH";
        } else {
            return "NOT_MATCH";
        }
    }

    public static function getPrivilege($desc)
    {
        $user = Auth::user();
        $data = Privilege::where('description', $desc)->where('role_id', $user->role)->first();
        return $data;
    }

    public static function getRoleName($roleId)
    {
        $roleName = Role::where('id', $roleId)->pluck('title')->first();
        return $roleName;
    }

    public static function customRound($number)
    {
        // $integerPart = intval($number);
        $integerPart = floor($number);

        $decimalPart = $number - $integerPart;
        // dd($number, $integerPart, $decimalPart);

        $roundedDecimal = round($decimalPart, 2);
        // $roundedDecimal = round($decimalPart, 1);

        if ($roundedDecimal >= 0.5) {
            return ceil($number);
        } else {
            return floor($number);
        }
    }

    public static function getChannelBankId($text)
    {
        $bankId = Channel::where('channel', $text)->pluck('bank_id')->first();
        return $bankId;
    }

    public static function getChannel($bankId)
    {
        $channel = Channel::where('bank_id', $bankId)->pluck('channel')->first();
        return $channel;
    }

    public static function convertDateFormat($date) {
        $inputFormat = 'd/m/Y'; // Format input
        $outputFormat = 'Y-m-d H:i:s'; // Format output
    
        // Cek apakah tanggal valid dengan format d/m/Y
        try {
            $carbonDate = Carbon::createFromFormat($inputFormat, $date);
    
            // Jika format valid, atur waktu default
            $carbonDate->hour = 0;
            $carbonDate->minute = 0;
            $carbonDate->second = 0;
    
            // Format tanggal ke format baru
            return $carbonDate->format($outputFormat);
        } catch (InvalidArgumentException $e) {
            // Format tidak valid
            return false;
        }
    }
    
    public static function isDateInFormat($date, $format) {
        try {
            $parsedDate = Carbon::createFromFormat($format, $date);
            // Pastikan juga bahwa format yang diinput benar-benar sesuai
            return $parsedDate->format($format) === $date;
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    public static function BNIconvertDateFormat($date) {
        $inputFormat = 'd/m/y H.i.s'; // Format input
        $outputFormat = 'Y-m-d H:i:s'; // Format output
    
        // Cek apakah tanggal valid dengan format d/m/y H.i.s
        try {
            $carbonDate = Carbon::createFromFormat($inputFormat, $date);
    
            // Format tanggal ke format baru
            return $carbonDate->format($outputFormat);
        } catch (InvalidArgumentException $e) {
            // Format tidak valid
            return false;
        }
    }
    
}
