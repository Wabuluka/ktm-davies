import { UseNewsFormReturn } from '@/Features/News/Hooks/useNewsForm';
import { useNewsPreviewMutation } from '@/Features/News/Hooks/useNewsPreviewMutation';
import {
  Button,
  useToast,
  List,
  ListItem,
  ButtonProps,
} from '@chakra-ui/react';

type Props = Omit<ButtonProps, 'children' | 'onClick'> & {
  formData: UseNewsFormReturn['data'];
  newsId?: string | number;
  siteId: string | number;
};

export function NewsPreviewButton({
  formData,
  newsId,
  siteId,
  isDisabled,
  ...props
}: Props) {
  const toast = useToast();
  const mutation = useNewsPreviewMutation({
    siteId,
    onSuccess: ({ preview }) => {
      window.open(preview.url, '_blank');
    },
    onError: (error) => {
      toast({
        title: 'プレビューURLの生成に失敗しました Failed to create preview URL',
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
    mutation.mutate({ id: newsId, formData });
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
