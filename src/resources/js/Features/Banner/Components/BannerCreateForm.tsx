import { useToast } from '@chakra-ui/react';
import { useBannerForm } from '@/Features/Banner/Hooks/useBannerForm';
import { BannerForm, BannerPlacement } from '@/Features/Banner';

type Props = {
  placementId: BannerPlacement['id'];
};

export function BannerCreateForm({ placementId }: Props) {
  const { data, setData, storeBanner, errors, processing } = useBannerForm();
  const toast = useToast();

  function handleSubmit(
    e: React.FormEvent<HTMLFormElement> | React.FormEvent<HTMLInputElement>,
  ) {
    e.preventDefault();
    storeBanner(placementId, {
      onSuccess: () =>
        toast({ title: `Created ${data.name}`, status: 'success' }),
      onError: () => {
        toast({ title: 'Failed to save', status: 'error' });
      },
    });
  }

  return (
    <BannerForm
      {...{ data, errors, setData, processing }}
      onSubmit={handleSubmit}
    />
  );
}
