import {
  Box,
  Hide,
  HStack,
  Icon,
  LinkBox,
  List,
  ListItem,
  Select,
} from '@chakra-ui/react';
import { useEffect, useState } from 'react';
import {
  BsChevronDoubleLeft,
  BsChevronDoubleRight,
  BsChevronLeft,
  BsChevronRight,
} from 'react-icons/bs';
import { Link } from './Link';

type Props = {
  pageChange: (index: number) => void;
  links: string[];
  currentIndex: number;
};

export const DrawerPagenator = ({ pageChange, links, currentIndex }: Props) => {
  const [value, setValue] = useState(links.at(currentIndex));

  useEffect(() => {
    setValue(links.at(currentIndex));
  }, [currentIndex, links]);

  if (links.length <= 1) return null;

  const lastIndex = links.length - 1;

  const isFirstPage = currentIndex === 0;
  const isLastPage = currentIndex === lastIndex;

  const firstPageUrl = links.at(0) as string;
  const lastPageUrl = links.at(-1) as string;

  const prevPageUrl = currentIndex > 0 && links.at(currentIndex - 1);
  const nextPageUrl = currentIndex < lastIndex && links.at(currentIndex + 1);

  const onChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    const selectedIndex = e.target.selectedIndex;
    const selectedPage = selectedIndex - 1;
    pageChange(selectedPage);
  };

  const handleFirstPage = (
    e:
      | React.KeyboardEvent<HTMLAnchorElement>
      | React.MouseEvent<HTMLAnchorElement, MouseEvent>,
  ) => {
    e.preventDefault();

    pageChange(0);
  };

  const handlePrevPage = (
    e:
      | React.KeyboardEvent<HTMLAnchorElement>
      | React.MouseEvent<HTMLAnchorElement, MouseEvent>,
  ) => {
    e.preventDefault();

    pageChange(currentIndex - 1);
  };

  const handleNextPage = (
    e:
      | React.KeyboardEvent<HTMLAnchorElement>
      | React.MouseEvent<HTMLAnchorElement, MouseEvent>,
  ) => {
    e.preventDefault();

    pageChange(currentIndex + 1);
  };

  const handleLastPage = (
    e:
      | React.KeyboardEvent<HTMLAnchorElement>
      | React.MouseEvent<HTMLAnchorElement, MouseEvent>,
  ) => {
    e.preventDefault();

    pageChange(lastIndex);
  };

  return (
    <Box as="nav">
      <List as={HStack} justifyContent="center" borderBottomWidth={2} py={4}>
        <ListItem as={LinkBox}>
          {!isFirstPage && (
            <HStack as={LinkBox}>
              <Icon fontSize={24} color="cyan.800" as={BsChevronDoubleLeft} />
              <Link href={firstPageUrl} overlay onClick={handleFirstPage}>
                <Hide below="sm">First</Hide>
              </Link>
            </HStack>
          )}
        </ListItem>

        <ListItem as={LinkBox}>
          {prevPageUrl && (
            <HStack as={LinkBox}>
              <Icon fontSize={20} color="cyan.800" as={BsChevronLeft} />
              <Link href={prevPageUrl} overlay onClick={handlePrevPage}>
                <Hide below="sm">Prev</Hide>
              </Link>
            </HStack>
          )}
        </ListItem>

        <ListItem>
          <Select placeholder="-" value={value} onChange={onChange}>
            {links.map((link, i) => (
              <option key={i} value={link}>
                {i + 1}
              </option>
            ))}
          </Select>
        </ListItem>

        <ListItem>
          {nextPageUrl && (
            <HStack as={LinkBox}>
              <Link href={nextPageUrl} overlay onClick={handleNextPage}>
                <Hide below="sm">Next</Hide>
              </Link>
              <Icon fontSize={20} color="cyan.800" as={BsChevronRight} />
            </HStack>
          )}
        </ListItem>

        <ListItem>
          {!isLastPage && (
            <HStack as={LinkBox}>
              <Link href={lastPageUrl} overlay onClick={handleLastPage}>
                <Hide below="sm">Last</Hide>
              </Link>
              <Icon fontSize={24} color="cyan.800" as={BsChevronDoubleRight} />
            </HStack>
          )}
        </ListItem>
      </List>
    </Box>
  );
};
