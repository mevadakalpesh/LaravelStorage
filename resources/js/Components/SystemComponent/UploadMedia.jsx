import {useForm } from '@inertiajs/react';
import Modal from '@/Components/Modal';
import SecondaryButton from '@/Components/SecondaryButton';
import {useState,useEffect} from 'react';
import { toast } from 'react-toastify';
import DangerButton from '@/Components/DangerButton';


export default function UploadMedia({
  folder,
  onFinish
}) 
{
  const [modelStatus,setModelStatus] = useState(false);
  const { data, setData, post, progress } = useForm({
    file: null,
    parent_id:null
  });

useEffect(()=>{
    
    if(folder && folder.id){
      setData(function (prev){
        return {...prev,parent_id:folder.id};
      });
    }
    
  },[]);

  const closeModal = () => {
    setModelStatus(!modelStatus);
  }
  
  const submit = (e) => {
    e.preventDefault();
    post(route('uploadMedia'),{
      forceFormData: true,
      onSuccess: (res) => {
       setModelStatus(false);
       toast('File Uploaded ..!');
       onFinish();
      },
    });
  }

    return (
        <>
           <button onClick={() => closeModal()} 
           class="mt-3 ml-2 inline-flex items-center px-4 py-2 bg-blue-600 border
           border-transparent rounded-md font-semibold text-xs text-white
           uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700
           focus:outline-none focus:ring-2 focus:ring-blue-500
           focus:ring-offset-2 transition ease-in-out duration-150 ">
           Upload 
           </button>
         
           <Modal show={modelStatus} onClose={closeModal}>
           
           <form onSubmit={(e) => submit(e)}  className="p-6">
              <h2 className="text-lg font-medium text-gray-900">
              Upload Folder / File
              </h2>

              <div class="flex items-center justify-center w-full">
                  <input
                      onChange={(e) => setData('file',e.target.files[0])}
                      type="file"
                  />
              </div>


                <div className="mt-6 flex justify-end">
                      <DangerButton >Submit</DangerButton>
                      <SecondaryButton onClick={closeModal}>Cancel</SecondaryButton>
                </div>
           </form>
          </Modal>
          </>
    );
}
