import FolderCreate from '@/Components/SystemComponent/FolderCreate';
import UploadMedia from '@/Components/SystemComponent/UploadMedia';

import Breadcrum from '@/Components/SystemComponent/Breadcrum';

import {useState,useEffect} from 'react';
import {getApi} from '@/Service/ApiService';
import { usePage,router} from '@inertiajs/react';

export default function FilesList({
  folder
}) 
{
   const [fileData,setFileData] = useState(null);
   const [breadcumData,setBreadcumData] = useState([]);
   
   
   useEffect(() => {
     loadFiles();
     
  
     if(folder && folder.slug)
     {
       getBradcrum(folder.slug);
     }
     
   },[]);
   
   const gotoFolder = (folder) => {
     if(folder.is_folder == 1)
     {
       router.visit(route('home',{folder:folder.slug}));
     }
   }
   
   const getBradcrum = (currentPath) => {
     
     getApi(route('getBreadcrum',{path:currentPath}))
     .then((res)=>{
       setBreadcumData(res.data.result);
     });
     
   }
   
   const loadFiles = () => {
     
     getApi(route('getFiles',{folder:folder?.id})).then((response) => {
       setFileData(response.data.result);
     });
     
   }
   
    return (
        <>
              <div class="relative overflow-x-auto mt-5 shadow-md sm:rounded-lg ">
                  <table class="w-full text-sm text-left rtl:text-right text-gray-500 light:text-gray-400">
                      <caption class="p-5 text-lg font-semibold text-left rtl:text-right text-gray-900 bg-white light:text-white light:bg-gray-800">
                          <FolderCreate onFinish={loadFiles} folder={folder} />
                          
                          <UploadMedia folder={folder} onFinish={loadFiles}/>
                          
                          <p class="mt-1 text-sm font-normal text-gray-500 light:text-gray-400">Browse a list of Flowbite products designed to help you work and play, stay organized, get answers, keep in touch, grow your business, and more.</p>
                          <Breadcrum BreadcrumData={breadcumData} />
                      </caption>
                      <thead class="text-xs text-gray-700 uppercase bg-gray-50 light:bg-gray-700 light:text-gray-400">
                          <tr>
                              <th scope="col" class="px-6 py-3">
                                  Name
                              </th>
                              <th scope="col" class="px-6 py-3">
                                  Type
                              </th>
                              <th scope="col" class="px-6 py-3">
                                  Size
                              </th>
                          </tr>
                      </thead>
                      <tbody>
                      {
                        fileData && fileData.length > 0 ?
                          fileData.map((data,index) => 
                                 <tr key={index} onClick={() =>
                                 gotoFolder(data)} class="bg-white border-b
                                 light:bg-gray-800 light:border-gray-700">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap light:text-white">
                                    
                                    <div className="flex items-center gap-4">
                                        {
                                          data.is_folder == 1 ?
                                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-6 h-6">
                                             <path strokeLinecap="round" strokeLinejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                                          </svg>
                                          : 
                                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-6 h-6">
                                              <path strokeLinecap="round" strokeLinejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                            </svg>

                                        }
                                        
                                        
                                        <p>{data.name}</p>
                                    </div>
                                    
                                    </th>
                                    <td class="px-6 py-4">
                                        {data.type}
                                    </td>
                                    <td class="px-6 py-4">
                                        {data.size}
                                    </td>
                                </tr>
                          )
                        : 'No Media Found'
                      }
                      </tbody>
                  </table>
              </div>
        </>
    );
}
