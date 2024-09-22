import { UseEditPageReturn } from '@/Features/Page/Hooks/useEditPage';
import { usePagePreviewMutation } from '@/Features/Page/Hooks/usePagePreviewMutation';
import {
  Button,
  ButtonProps,
  List,
  ListItem,
  useToast,
} from '@chakra-ui/react';

type Props = Omit<ButtonProps, 'children' | 'onClick'> & {
  formData: UseEditPageReturn['data'];
  pageId?: string | number;
  siteId: string | number;
};

export function PagePreviewButton({
  formData,
  pageId,
  siteId,
  isDisabled,
  ...props
}: Props) {
  const toast = useToast();
  const mutation = usePagePreviewMutation({
    siteId,
    onSuccess: ({ preview }) => {
      window.open(preview.url, '_blank');
    },
    onError: (error) => {
      toast({
        title:
          'プレビューURLの生成に失敗しました Failed to generate preview URL',
        status: 'error',
      });
      const errors: string[] = [];
      Object.entries(error.response?.data.errors ?? {}).map(([_, messages]) =>
        messages.forEach((m) => errors.push(m)),
      );
      if (errors.length === 0) {
        return;
      }
      const reason = (
        <List spacing={2} listStyleType="initial">
          {errors.map((error) => (
            <ListItem key={error}>{error}</ListItem>
          ))}
        </List>
      );
      toast({ title: reason, status: 'error' });
    },
  });
  function handlePreview() {
    mutation.mutate({ id: pageId, formData });
  }

  return (
    <Button
      onClick={handlePreview}
      isDisabled={isDisabled || mutation.isLoading}
      {...props}
    >
      Preview
    </Button>
  );
}
