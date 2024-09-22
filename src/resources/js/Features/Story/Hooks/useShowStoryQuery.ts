import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import axios from 'axios';
import { useQuery } from 'react-query';
import { Story } from '@/Features/Story';

const fetchStory = (id: number) => {
  return axios.get(route('stories.show', id)).then((response) => response.data);
};

type Args = [
  id: number,
  options?: Parameters<typeof useQuery<Story, Error>>[2],
];

export const useShowStoryQuery = (...[id, options]: Args) => {
  const queryKey = useQueryKeys().stories.show(id);
  const query = useQuery<Story, Error>(queryKey, () => fetchStory(id), options);

  return { ...query, queryKey };
};
