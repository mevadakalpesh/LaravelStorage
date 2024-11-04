import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head,useForm } from '@inertiajs/react';

import Modal from '@/Components/Modal';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import DangerButton from '@/Components/DangerButton';
import SecondaryButton from '@/Components/SecondaryButton';
import InputError from '@/Components/InputError';

import {useState} from 'react';

export default function Dashboard({ auth }) {
  const [modelStatus,setModelStatus] = useState(false);
  const {data,setData,post,error} = useForm({
    name:null
  });
  const closeModal = () => {
    setModelStatus(!modelStatus);
  }
  
  const handleInput = (event) => {
    let name = event.target.name;
    let value = event.target.value;
    
    setData(function (prev){
      return {...prev,[name]:value};
    });
  } 
  const createFolder =(event) => {
    event.preventDefault();
    post(route('addFolder'),data,{
      onSuccess:function () {
        
      }
    });
    
  }
  
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>}
        >
            <Head title="Dashboard" />
           <button onClick={() => closeModal()} class="inline-flex items-center px-4 py-2 bg-blue-600 border
           border-transparent rounded-md font-semibold text-xs text-white
           uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700
           focus:outline-none focus:ring-2 focus:ring-blue-500
           focus:ring-offset-2 transition ease-in-out duration-150 ">
              Create Folder
           </button>
           <Modal show={modelStatus} onClose={closeModal}>
           
           <form onSubmit={createFolder} className="p-6">
              <h2 className="text-lg font-medium text-gray-900">
                  add New Folder
              </h2>
              
        
                <div className="mt-6">
                      <TextInput
                        id="name"
                        type="text"
                        name="name"
                        className="mt-1 block w-3/4"
                        isFocused
                        onChange={(e) => handleInput(e)}
                        placeholder="Folder Name"
                    />
                    <InputError  className="mt-2" />
                </div>
                
                <div className="mt-6 flex justify-end">
                        <SecondaryButton onClick={closeModal}>Cancel</SecondaryButton>

                        <DangerButton 
                        className="ms-3" 
                        >
                            Create Button
                        </DangerButton>
                    </div>
                
                
           </form>
           
          </Modal>
          
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">You're logged in!</div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
