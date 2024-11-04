import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

import { Head,useForm } from '@inertiajs/react';

import FilesList from '@/Components/SystemComponent/FilesList';
import {useState} from 'react';


export default function FileManage({ auth,folder}) {
 
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">FileManage</h2>}
        >
            <Head title="FileManage" />
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
              <FilesList folder={folder} />
            </div>
          
        </AuthenticatedLayout>
    );
}
