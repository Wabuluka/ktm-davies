import React from 'react';
import { Tr, Td, Checkbox, Center, LinkBox, useToast } from '@chakra-ui/react';
import { Banner } from '@/Features/Banner/Types';
import { PreviewableThumbnail } from '@/UI/Components/MediaAndIcons/PreviewableThumbnail';
import { Link } from '@/UI/Components/Navigation/Link';
import { SortButtons } from '@/UI/Components/Form/Button/SortButtons';
import { router } from '@inertiajs/react';

type Props = {
  banner: Banner;
  isFirst: boolean;
  isLast: boolean;
};

export const BannerListItem = ({
  banner,
  isFirst = false,
  isLast = false,
}: Props) => {
  const toast = useToast();

  function handleOrderUp() {
    router.patch(
      route('banners.sort.move-up', {
        banner: banner.id,
      }),
      undefined,
      {
        onSuccess: () =>
          toast({ title: '表示順をSaved successfully', status: 'success' }),
        preserveScroll: true,
      },
    );
  }
  function handleOrderDown() {
    router.patch(
      route('banners.sort.move-down', {
        banner: banner.id,
      }),
      undefined,
      {
        onSuccess: () =>
          toast({
            title: 'Saved the sorting order successfully',
            status: 'success',
          }),
        preserveScroll: true,
      },
    );
  }

  return (
    <LinkBox as={Tr} _hover={{ bg: 'gray.100' }}>
      <Td>
        <Link
          overlay
          href={route('banners.edit', {
            banner_placement: banner.placement_id,
            banner: banner.id,
          })}
          aria-label={`Edit ${banner.name}`}
        />
        {banner.name}
      </Td>
      <Td>{banner.url}</Td>
      <Td>
        <Center>
          <Checkbox defaultChecked={banner.new_tab} disabled></Checkbox>
        </Center>
      </Td>
      <Td>
        <Center>
          <Checkbox defaultChecked={banner.displayed} disabled></Checkbox>
        </Center>
      </Td>
      <Td p="0">
        {banner.image?.original_url && (
          <PreviewableThumbnail
            previewTriggerProps={{
              w: '100%',
              p: 1,
              'aria-label': 'Preview thumbnail',
            }}
            imageProps={{
              src: banner.image.original_url,
              alt: '',
            }}
          />
        )}
      </Td>
      <Td>
        <SortButtons
          disableUp={isFirst}
          disableDown={isLast}
          onUp={handleOrderUp}
          onDown={handleOrderDown}
        />
      </Td>
    </LinkBox>
  );
};
