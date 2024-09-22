import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import axios from 'axios';
import { useQuery } from 'react-query';
import { Story } from '@/Features/Story';

export type QueryParams = {
  title?: string;
  currentIndex?: number;
};

const fetchStories = (queryParams?: QueryParams) => {
  return axios
    .get(route('stories.index'), {
      params: { title: queryParams?.title, page: queryParams?.currentIndex },
    })
    .then((response) => response.data);
};

export const useIndexStoriesQuery = (queryParams?: QueryParams) => {
  const queryKeys = useQueryKeys();
  const queryKey = queryParams
    ? queryKeys.stories.filtered(queryParams)
    : queryKeys.stories.all;
  const query = useQuery<[Story[], number], Error>(queryKey, () =>
    fetchStories(queryParams),
  );

  const stories = query.data?.[0];
  const lastPage = query.data?.[1] ?? 1;

  return { ...query, queryKey, stories, lastPage };
};
