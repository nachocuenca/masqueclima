#!/usr/bin/env node
import { cp, mkdir, rm } from "node:fs/promises";
import { existsSync } from "node:fs";
import path from "node:path";

const root = process.cwd();
const standalone = path.join(root, ".next", "standalone");

if (!existsSync(standalone)) {
  process.exit(0);
}

async function copyIntoStandalone(source, destination) {
  const sourcePath = path.join(root, source);
  const destinationPath = path.join(standalone, destination);

  if (!existsSync(sourcePath)) {
    return;
  }

  await rm(destinationPath, { force: true, recursive: true });
  await mkdir(path.dirname(destinationPath), { recursive: true });
  await cp(sourcePath, destinationPath, { recursive: true });
}

await copyIntoStandalone("public", "public");
await copyIntoStandalone(path.join(".next", "static"), path.join(".next", "static"));
