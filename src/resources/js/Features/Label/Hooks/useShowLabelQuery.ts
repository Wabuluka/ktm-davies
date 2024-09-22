import { useQueryKeys } from '@/Features/Api/Hooks/useQueryKeys';
import axios from 'axios';
import { useQuery } from 'react-query';
import { Label } from '../Types';

const fetchLabel = (id: number) => {
  return axios.get(route('label.show', id)).then((response) => response.data);
};

type Args = [
  id: number,
  options?: Parameters<typeof useQuery<Label, Error>>[2],
];

export const useShowLabelQuery = (...[id, options]: Args) => {
  const queryKey = useQueryKeys().label.show(id);
  const query = useQuery<Label, Error>(queryKey, () => fetchLabel(id), options);

  return { ...query, queryKey };
};
