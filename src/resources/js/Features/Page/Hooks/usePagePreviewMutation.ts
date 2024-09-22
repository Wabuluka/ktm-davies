import { useMutation } from 'react-query';
import axios, { AxiosError, AxiosResponse } from 'axios';
import { LaravelValidationError } from '@/Features/Misc/Api/Types';
import { PagePreview } from '@/Features/Page/Types';
import { UseEditPageReturn } from '@/Features/Page/Hooks/useEditPage';

type PagePreviewResponse = {
  preview: PagePreview;
};

type Props = {
  siteId: string | number;
  onSuccess: (response: PagePreviewResponse) => void;
  onError?: (error: AxiosError<LaravelValidationError>) => void;
};

export function usePagePreviewMutation({ siteId, onSuccess, onError }: Props) {
  return useMutation<
    AxiosResponse<PagePreviewResponse>,
    AxiosError<LaravelValidationError>,
    {
      id?: string | number;
      formData: UseEditPageReturn['data'];
    }
  >({
    mutationFn: ({ id, formData }) => {
      return axios.post(
        route('api.sites.pages.preview', { site: siteId, page: id }),
        formData,
      );
    },
    onSuccess: (response) => {
      onSuccess?.(response.data);
    },
    onError: (error) => {
      onError?.(error);
    },
  });
}
