import axios from 'axios';

const getApi = (url,data = {}) => {
  return axios.get(url,data);
}

const getPost = (url,data = {}) => {
  return axios.post(url,data);
}

export {
  getApi,
  getPost
}